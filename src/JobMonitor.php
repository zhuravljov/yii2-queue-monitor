<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\queue\ErrorEvent;
use yii\queue\ExecEvent;
use yii\queue\JobEvent;
use yii\queue\PushEvent;
use yii\queue\Queue;
use zhuravljov\yii\queue\monitor\records\ExecRecord;
use zhuravljov\yii\queue\monitor\records\PushRecord;
use zhuravljov\yii\queue\monitor\records\WorkerRecord;

/**
 * Queue Job Monitor
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class JobMonitor extends Behavior
{
    /**
     * @var Queue
     * @inheritdoc
     */
    public $owner;
    /**
     * @var bool|callable
     */
    public $storePushTrace = true;
    /**
     * @var bool|callable
     */
    public $storePushEnv = true;
    /**
     * @var Env
     */
    protected $env;
    /**
     * @var null|PushRecord
     */
    protected static $startedPush;

    /**
     * @param Env $env
     * @param array $config
     */
    public function __construct(Env $env, $config = [])
    {
        $this->env = $env;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Queue::EVENT_AFTER_PUSH => 'afterPush',
            Queue::EVENT_BEFORE_EXEC => 'beforeExec',
            Queue::EVENT_AFTER_EXEC => 'afterExec',
            Queue::EVENT_AFTER_ERROR => 'afterError',
        ];
    }

    /**
     * @param PushEvent $event
     */
    public function afterPush(PushEvent $event)
    {
        $push = new PushRecord();
        $push->parent_id = static::$startedPush ? static::$startedPush->id : null;
        $push->sender_name = $this->getSenderName($event);
        $push->job_uid = $event->id;
        $push->setJob($event->job);
        $push->push_ttr = $event->ttr;
        $push->push_delay = $event->delay;
        $this->fillPushTrace($push, $event);
        $this->fillPushEnv($push, $event);
        $push->pushed_at = time();
        $push->save(false);
    }

    /**
     * @param ExecEvent $event
     */
    public function beforeExec(ExecEvent $event)
    {
        static::$startedPush = $push = $this->getPushRecord($event);
        if (!$push) {
            return;
        }
        if ($push->isStopped()) {
            // Rejects job execution in case is stopped
            $event->handled = true;
            return;
        }
        $this->env->db->transaction(function () use ($event, $push) {
            $worker = $this->getWorkerRecord($event);

            $exec = new ExecRecord();
            $exec->push_id = $push->id;
            if ($worker) {
                $exec->worker_id = $worker->id;
            }
            $exec->attempt = $event->attempt;
            $exec->reserved_at = time();
            $exec->save(false);

            $push->first_exec_id = $push->first_exec_id ?: $exec->id;
            $push->last_exec_id = $exec->id;
            $push->save(false);

            if ($worker) {
                $worker->last_exec_id = $exec->id;
                $worker->save(false);
            }
        });
    }

    /**
     * @param ExecEvent $event
     */
    public function afterExec(ExecEvent $event)
    {
        $push = static::$startedPush ?: $this->getPushRecord($event);
        if (!$push) {
            return;
        }
        if ($push->last_exec_id) {
            ExecRecord::updateAll([
                'done_at' => time(),
                'memory_usage' => memory_get_peak_usage(),
                'error' => null,
                'retry' => false,
            ], [
                'id' => $push->last_exec_id
            ]);
        }
    }

    /**
     * @param ErrorEvent $event
     */
    public function afterError(ErrorEvent $event)
    {
        $push = static::$startedPush ?: $this->getPushRecord($event);
        if (!$push) {
            return;
        }
        if ($push->isStopped()) {
            // Breaks retry in case is stopped
            $event->retry = false;
        }
        if ($push->last_exec_id) {
            ExecRecord::updateAll([
                'done_at' => time(),
                'memory_usage' => static::$startedPush ? memory_get_peak_usage() : null,
                'error' => $event->error,
                'retry' => $event->retry,
            ], [
                'id' => $push->last_exec_id
            ]);
        }
    }

    /**
     * @param JobEvent $event
     * @throws
     * @return string
     */
    protected function getSenderName($event)
    {
        foreach (Yii::$app->getComponents(false) as $id => $component) {
            if ($component === $event->sender) {
                return $id;
            }
        }
        throw new InvalidConfigException('Queue must be an application component.');
    }

    /**
     * @param PushRecord $record
     * @param PushEvent $event
     */
    protected function fillPushTrace(PushRecord $record, PushEvent $event)
    {
        $record->push_trace_data = null;

        $canStore = is_callable($this->storePushTrace)
            ? call_user_func($this->storePushTrace, $event)
            : $this->storePushTrace;
        if (!$canStore) {
            return;
        }

        $record->push_trace_data = (new \Exception())->getTraceAsString();
    }

    /**
     * @param PushRecord $record
     * @param PushEvent $event
     */
    protected function fillPushEnv(PushRecord $record, PushEvent $event)
    {
        $record->push_env_data = null;

        $canStore = is_callable($this->storePushEnv)
            ? call_user_func($this->storePushEnv, $event)
            : $this->storePushEnv;
        if (!$canStore) {
            return;
        }

        $values = $_SERVER;
        ksort($values);
        $record->push_env_data = serialize($values);
    }

    /**
     * @param JobEvent $event
     * @return PushRecord
     */
    protected function getPushRecord(JobEvent $event)
    {
        if ($event->id !== null) {
            return $this->env->db->useMaster(function () use ($event) {
                return PushRecord::find()
                    ->byJob($this->getSenderName($event), $event->id)
                    ->one();
            });
        } else {
            return null;
        }
    }

    /**
     * @param ExecEvent $event
     * @return WorkerRecord|null
     */
    protected function getWorkerRecord(ExecEvent $event)
    {
        if ($event->sender->getWorkerPid() === null) {
            return null;
        }
        if (!$this->isWorkerMonitored()) {
            return null;
        }

        return $this->env->db->useMaster(function () use ($event) {
            return WorkerRecord::find()
                ->byPid($event->sender->getWorkerPid())
                ->active(false)
                ->one();
        });
    }

    /**
     * @return bool whether workers are monitored.
     */
    private function isWorkerMonitored()
    {
        foreach ($this->owner->getBehaviors() as $behavior) {
            if ($behavior instanceof WorkerMonitor) {
                return true;
            }
        }
        return false;
    }
}
