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
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\queue\ExecEvent;
use yii\queue\JobEvent;
use yii\queue\JobInterface;
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
     * @var array of job class names that this behavior should tracks.
     * @since 0.3.2
     */
    public $only = [];
    /**
     * @var array of job class names that this behavior should not tracks.
     * @since 0.3.2
     */
    public $except = [];
    /**
     * @var array
     */
    public $contextVars = [
        '_SERVER.argv',
        '_SERVER.REQUEST_METHOD',
        '_SERVER.REQUEST_URI',
        '_SERVER.HTTP_REFERER',
        '_SERVER.HTTP_USER_AGENT',
        '_POST',
    ];
    /**
     * @var Queue
     * @inheritdoc
     */
    public $owner;
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
            Queue::EVENT_AFTER_ERROR => 'afterExec',
        ];
    }

    /**
     * @param PushEvent $event
     */
    public function afterPush(PushEvent $event)
    {
        if (!$this->isActive($event->job)) {
            return;
        }

        if ($this->env->db->getTransaction()) {
            // create new database connection, if there is an open transaction
            // to ensure insert statement is not affected by a rollback
            $this->env->db = clone $this->env->db;
        }

        $push = new PushRecord();
        $push->parent_id = static::$startedPush ? static::$startedPush->id : null;
        $push->sender_name = $this->getSenderName($event);
        $push->job_uid = $event->id;
        $push->setJob($event->job);
        $push->ttr = $event->ttr;
        $push->delay = $event->delay;
        $push->trace = (new \Exception())->getTraceAsString();
        $push->context = $this->getContext();
        $push->pushed_at = time();
        $push->save(false);
    }

    /**
     * @param ExecEvent $event
     */
    public function beforeExec(ExecEvent $event)
    {
        if (!$this->isActive($event->job)) {
            return;
        }
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
            $exec->started_at = time();
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
        if (!$this->isActive($event->job)) {
            return;
        }
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
                'finished_at' => time(),
                'memory_usage' => static::$startedPush ? memory_get_peak_usage() : null,
                'error' => $event->error,
                'result_data' => $event->result !== null ? serialize($event->result) : null,
                'retry' => $event->retry,
            ], [
                'id' => $push->last_exec_id
            ]);
        }
    }

    /**
     * @param JobInterface $job
     * @return bool
     * @since 0.3.2
     */
    protected function isActive(JobInterface $job)
    {
        $onlyMatch = true;
        if ($this->only) {
            $onlyMatch = false;
            foreach ($this->only as $className) {
                if (is_a($job, $className)) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        $exceptMatch = false;
        foreach ($this->except as $className) {
            if (is_a($job, $className)) {
                $exceptMatch = true;
                break;
            }
        }

        return !$exceptMatch && $onlyMatch;
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
     * @return string
     */
    protected function getContext()
    {
        $context = ArrayHelper::filter($GLOBALS, $this->contextVars);
        $result = [];
        foreach ($context as $key => $value) {
            $result[] = "\${$key} = " . VarDumper::dumpAsString($value);
        }

        return implode("\n\n", $result);
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
                ->byEvent($this->env->getHost(), $event->sender->getWorkerPid())
                ->active()
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
