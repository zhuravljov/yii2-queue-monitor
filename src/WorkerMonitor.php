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
use yii\console\ExitCode;
use yii\queue\cli\Queue;
use yii\queue\cli\WorkerEvent;
use zhuravljov\yii\queue\monitor\records\WorkerRecord;

/**
 * Queue Worker Monitor
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class WorkerMonitor extends Behavior
{
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
     * @var WorkerRecord
     */
    private $record;

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
        $events = [
            Queue::EVENT_WORKER_START => 'workerStart',
            Queue::EVENT_WORKER_STOP => 'workerStop',
        ];
        if ($this->env->canListenWorkerLoop()) {
            $events[Queue::EVENT_WORKER_LOOP] = 'workerLoop';
        }
        return $events;
    }

    /**
     * @param WorkerEvent $event
     */
    public function workerStart(WorkerEvent $event)
    {
        $this->record = new WorkerRecord();
        $this->record->sender_name = $this->getSenderName($event);
        $this->record->host = $this->env->getHost();
        $this->record->pid = $event->sender->getWorkerPid();
        $this->record->started_at = time();
        $this->record->pinged_at = time();
        $this->record->save(false);
    }

    /**
     * @param WorkerEvent $event
     */
    public function workerLoop(WorkerEvent $event)
    {
        if ($this->record->pinged_at + $this->env->workerPingInterval > time()) {
            return;
        }
        if (!$this->record->refresh()) {
            $this->record->setIsNewRecord(true);
        }
        $this->record->pinged_at = time();
        $this->record->save(false);

        if ($this->record->isStopped()) {
            $event->exitCode = ExitCode::OK;
        }
    }

    /**
     * @param WorkerEvent $event
     */
    public function workerStop(WorkerEvent $event)
    {
        if (!$this->env->canListenWorkerLoop()) {
            $this->env->db->close(); // To reopen a lost connection
        }
        $this->record->finished_at = time();
        $this->record->save(false);
    }

    /**
     * @param WorkerEvent $event
     * @throws
     * @return string
     */
    protected function getSenderName(WorkerEvent $event)
    {
        foreach (Yii::$app->getComponents(false) as $id => $component) {
            if ($component === $event->sender) {
                return $id;
            }
        }
        throw new InvalidConfigException('Queue must be an application component.');
    }
}
