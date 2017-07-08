<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\db\Connection;
use zhuravljov\yii\queue\ErrorEvent;
use zhuravljov\yii\queue\ExecEvent;
use zhuravljov\yii\queue\JobEvent;
use zhuravljov\yii\queue\monitor\records\ExecRecord;
use zhuravljov\yii\queue\monitor\records\PushRecord;
use zhuravljov\yii\queue\PushEvent;
use zhuravljov\yii\queue\Queue;

/**
 * Class Bootstrap
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Bootstrap extends Object implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::$container->setSingleton(Config::class);

        Event::on(Queue::class, Queue::EVENT_AFTER_PUSH, [$this, 'afterPush']);
        Event::on(Queue::class, Queue::EVENT_BEFORE_EXEC, [$this, 'beforeExec']);
        Event::on(Queue::class, Queue::EVENT_AFTER_EXEC, [$this, 'afterExec']);
        Event::on(Queue::class, Queue::EVENT_AFTER_ERROR, [$this, 'afterError']);
    }

    public function afterPush(PushEvent $event)
    {
        $push = new PushRecord();
        $push->sender = $this->getSenderName($event);
        $push->job_uid = $event->id;
        $push->job_class = get_class($event->job);
        $push->job_object = serialize($event->job);
        $push->ttr = $event->ttr;
        $push->delay = $event->delay;
        $push->pushed_at = time();
        $push->save(false);
    }

    public function beforeExec(ExecEvent $event)
    {
        /** @var Connection $db */
        $db = Yii::$container->get(Config::class)->db;
        $db->transaction(function () use ($event) {
            $push = $this->getPushRecord($event);

            $exec = new ExecRecord();
            $exec->push_id = $push->id;
            $exec->attempt = $event->attempt;
            $exec->reserved_at = time();
            $exec->save(false);

            $push->first_exec_id = $push->first_exec_id ?: $exec->id;
            $push->last_exec_id = $exec->id;
            $push->save(false);
        });
    }

    public function afterExec(ExecEvent $event)
    {
        ExecRecord::updateAll([
            'done_at' => time(),
        ], [
            'id' => $this->getPushRecord($event)->last_exec_id
        ]);
    }

    public function afterError(ErrorEvent $event)
    {
        ExecRecord::updateAll([
            'done_at' => time(),
            'error' => $event->error,
            'retry' => $event->retry,
        ], [
            'id' => $this->getPushRecord($event)->last_exec_id
        ]);
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
     * @return PushRecord
     */
    protected function getPushRecord(JobEvent $event)
    {
        return PushRecord::find()
            ->andWhere(['sender' => $this->getSenderName($event)])
            ->andWhere(['job_uid' => $event->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }
}