<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\records;

use Yii;
use yii\db\ActiveRecord;
use yii\queue\Job;
use yii\queue\Queue;
use zhuravljov\yii\queue\monitor\Env;

/**
 * Class PushRecord
 *
 * @property integer $id
 * @property string $sender_name
 * @property string $job_uid
 * @property string $job_class
 * @property string|resource $job_object
 * @property integer $ttr
 * @property integer $delay
 * @property integer $pushed_at
 * @property integer $stopped_at
 * @property integer $first_exec_id
 * @property integer $last_exec_id
 *
 * @property ExecRecord[] $execs
 * @property ExecRecord|null $firstExec
 * @property ExecRecord|null $lastExec
 * @property array $execCount
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class PushRecord extends ActiveRecord
{
    const STATUS_STOPPED = 'stopped';
    const STATUS_WAITING = 'waiting';
    const STATUS_STARTED = 'started';
    const STATUS_DONE = 'done';
    const STATUS_FAILED = 'failed';
    const STATUS_RESTARTED = 'restarted';
    const STATUS_BURIED = 'buried';

    private $_job;

    /**
     * @inheritdoc
     * @return PushQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PushQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$container->get(Env::class)->db;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$container->get(Env::class)->pushTableName;
    }

    /**
     * @return ExecQuery|\yii\db\ActiveQuery
     */
    public function getExecs()
    {
        return $this->hasMany(ExecRecord::class, ['push_id' => 'id']);
    }

    /**
     * @return ExecQuery|\yii\db\ActiveQuery
     */
    public function getFirstExec()
    {
        return $this->hasOne(ExecRecord::class, ['id' => 'first_exec_id']);
    }

    /**
     * @return ExecQuery|\yii\db\ActiveQuery
     */
    public function getLastExec()
    {
        return $this->hasOne(ExecRecord::class, ['id' => 'last_exec_id']);
    }

    /**
     * @return ExecQuery|\yii\db\ActiveQuery
     */
    public function getExecCount()
    {
        return $this->hasOne(ExecRecord::class, ['push_id' => 'id'])
            ->select(['push_id', 'attempts' => 'COUNT(*)', 'errors' => 'COUNT(error)'])
            ->groupBy('push_id')
            ->asArray();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        if ($this->isStopped()) {
            return self::STATUS_STOPPED;
        }
        if (!$this->lastExec) {
            return self::STATUS_WAITING;
        }
        if (!$this->lastExec->done_at && $this->lastExec->attempt == 1) {
            return self::STATUS_STARTED;
        }
        if ($this->lastExec->done_at && $this->lastExec->error === null) {
            return self::STATUS_DONE;
        }
        if ($this->lastExec->done_at && $this->lastExec->retry) {
            return self::STATUS_FAILED;
        }
        if (!$this->lastExec->done_at) {
            return self::STATUS_RESTARTED;
        }
        if ($this->lastExec->done_at && !$this->lastExec->retry) {
            return self::STATUS_BURIED;
        }
        return null;
    }

    /**
     * @return Queue|object|null
     */
    public function getSender()
    {
        return Yii::$app->get($this->sender_name, false);
    }

    /**
     * @return Job|mixed
     */
    public function getJob()
    {
        if ($this->_job === null) {
            // pgsql
            if (is_resource($this->job_object)) {
                $this->job_object = stream_get_contents($this->job_object);
            }
            $this->_job = unserialize($this->job_object);
        }
        return $this->_job;
    }

    /**
     * @param Job|mixed
     */
    public function setJob($job)
    {
        $this->job_class = get_class($job);
        $this->job_object = serialize($job);
        $this->_job = null;
    }

    /**
     * @return bool
     */
    public function isSenderValid()
    {
        return $this->getSender() instanceof Queue;
    }

    /**
     * @return bool
     */
    public function isJobValid()
    {
        return (gettype($this->getJob()) !== 'object') || ($this->getJob() instanceof Job);
    }

    /**
     * @return bool
     */
    public function canPushAgain()
    {
        return $this->isSenderValid() && $this->isJobValid();
    }

    /**
     * @return bool marked as stopped
     */
    public function isStopped()
    {
        return !!$this->stopped_at;
    }

    /**
     * @return bool ability to mark as stopped
     */
    public function canStop()
    {
        if ($this->isStopped()) {
            return false;
        }
        if ($this->lastExec && $this->lastExec->done_at && !$this->lastExec->retry) {
            return false;
        }
        return true;
    }

    /**
     * Marks as stopped
     */
    public function stop()
    {
        $this->stopped_at = time();
        $this->save(false);
    }
}
