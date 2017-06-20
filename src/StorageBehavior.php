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
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use zhuravljov\yii\queue\ErrorEvent;
use zhuravljov\yii\queue\ExecEvent;
use zhuravljov\yii\queue\JobEvent;
use zhuravljov\yii\queue\PushEvent;
use zhuravljov\yii\queue\Queue;

/**
 * Class StorageBehavior
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class StorageBehavior extends Behavior
{
    /**
     * @var Connection|array|string
     */
    public $db = 'db';
    /**
     * @var string
     */
    public $pushTableName = '{{%queue_push}}';
    /**
     * @var string
     */
    public $execTableName = '{{%queue_exec}}';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
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
        $this->db->createCommand()->insert($this->pushTableName, [
            'sender' => $this->getSenderName($event),
            'job_uid' => $event->id,
            'job_class' => get_class($event->job),
            'job_object' => serialize($event->job),
            'ttr' => $event->ttr,
            'delay' => $event->delay,
            'pushed_at' => time(),
        ])->execute();
    }

    /**
     * @param ExecEvent $event
     */
    public function beforeExec(ExecEvent $event)
    {
        $this->db->transaction(function () use ($event) {
            $push = $this->getPushRecord($event);
            $this->db->createCommand()->insert($this->execTableName, [
                'push_id' => $push['id'],
                'attempt' => $event->attempt,
                'reserved_at' => time(),
            ])->execute();
            $this->db->createCommand()->update($this->pushTableName, [
                'first_exec_id' => $push['first_exec_id'] ?: $this->db->lastInsertID,
                'last_exec_id' => $this->db->lastInsertID,
            ], [
                'id' => $push['id'],
            ])->execute();
        });
    }

    /**
     * @param ExecEvent $event
     */
    public function afterExec(ExecEvent $event)
    {
        $this->db->createCommand()->update($this->execTableName, [
            'done_at' => time(),
        ], [
            'id' => $this->getPushRecord($event)['last_exec_id'],
        ])->execute();
    }

    /**
     * @param ErrorEvent $event
     */
    public function afterError(ErrorEvent $event)
    {
        $this->db->createCommand()->update($this->execTableName, [
            'done_at' => time(),
            'error' => $event->error,
            'retry' => $event->retry,
        ], [
            'id' => $this->getPushRecord($event)['last_exec_id'],
        ])->execute();
    }

    /**
     * @param JobEvent $event
     * @return string
     * @throws
     */
    protected function getSenderName(JobEvent $event)
    {
        foreach (Yii::$app->getComponents(false) as $id => $component) {
            if ($component === $event->sender) {
                return $id;
            }
        }
        throw new InvalidConfigException('Queue must be an application component.');
    }

    /**
     * @param JobEvent $event
     * @return array
     */
    protected function getPushRecord(JobEvent $event)
    {
        return (new Query())
            ->from($this->pushTableName)
            ->where([
                'sender' => $this->getSenderName($event),
                'job_uid' => $event->id,
            ])
            ->one($this->db);
    }
}