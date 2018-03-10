<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\records;

use Yii;
use yii\db\ActiveRecord;
use yii\queue\JobInterface;
use yii\queue\Queue;
use zhuravljov\yii\queue\monitor\Env;

/**
 * Class PushRecord
 *
 * @property int $id
 * @property string $sender_name
 * @property string $job_uid
 * @property string $job_class
 * @property string|resource $job_object
 * @property int $push_ttr
 * @property int $push_delay
 * @property string|null $push_trace_data
 * @property string|null $push_env_data
 * @property int $pushed_at
 * @property int|null $stopped_at
 * @property int|null $first_exec_id
 * @property int|null $last_exec_id
 *
 * @property ExecRecord[] $execs
 * @property ExecRecord|null $firstExec
 * @property ExecRecord|null $lastExec
 * @property array $execTotal
 *
 * @property int $attemptCount
 * @property int $waitTime
 * @property string $status
 *
 * @property Queue|null $sender
 * @property JobInterface $job
 * @property array $jobParams
 * @property string[] $pushTrace
 * @property array $pushEnv
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
        return Yii::createObject(PushQuery::class, [get_called_class()]);
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
     * @return ExecQuery
     */
    public function getExecs()
    {
        return $this->hasMany(ExecRecord::class, ['push_id' => 'id']);
    }

    /**
     * @return ExecQuery
     */
    public function getFirstExec()
    {
        return $this->hasOne(ExecRecord::class, ['id' => 'first_exec_id']);
    }

    /**
     * @return ExecQuery
     */
    public function getLastExec()
    {
        return $this->hasOne(ExecRecord::class, ['id' => 'last_exec_id']);
    }

    /**
     * @return ExecQuery
     */
    public function getExecTotal()
    {
        return $this->hasOne(ExecRecord::class, ['push_id' => 'id'])
            ->select([
                'push_id',
                'attempts' => 'COUNT(*)',
                'errors' => 'COUNT(error)',
            ])
            ->groupBy('push_id')
            ->asArray();
    }

    /**
     * @return int number of attempts
     */
    public function getAttemptCount()
    {
        return $this->execTotal['attempts'] ?: 0;
    }

    /**
     * @return int waiting time from push till first execute
     */
    public function getWaitTime()
    {
        if ($this->firstExec) {
            return $this->firstExec->reserved_at - $this->pushed_at - $this->push_delay;
        }
        return time() - $this->pushed_at - $this->push_delay;
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
        if (!$this->lastExec->isDone() && $this->lastExec->attempt == 1) {
            return self::STATUS_STARTED;
        }
        if ($this->lastExec->isDone() && !$this->lastExec->isFailed()) {
            return self::STATUS_DONE;
        }
        if ($this->lastExec->isDone() && $this->lastExec->retry) {
            return self::STATUS_FAILED;
        }
        if (!$this->lastExec->isDone()) {
            return self::STATUS_RESTARTED;
        }
        if ($this->lastExec->isDone() && !$this->lastExec->retry) {
            return self::STATUS_BURIED;
        }
        return null;
    }

    /**
     * @return Queue|null
     */
    public function getSender()
    {
        return Yii::$app->get($this->sender_name, false);
    }

    /**
     * @return bool
     */
    public function isSenderValid()
    {
        return $this->getSender() instanceof Queue;
    }

    /**
     * @param JobInterface|mixed $job
     */
    public function setJob($job)
    {
        $this->job_class = get_class($job);
        $this->job_object = serialize($job);
        $this->_job = null;
    }

    /**
     * @return JobInterface|mixed
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
     * @return bool
     */
    public function isJobValid()
    {
        return (gettype($this->getJob()) !== 'object') || ($this->getJob() instanceof JobInterface);
    }

    /**
     * @return array of job properties
     */
    public function getJobParams()
    {
        return get_object_vars($this->getJob());
    }

    /**
     * @return string[] trace lines since Queue::push()
     */
    public function getPushTrace()
    {
        if (is_resource($this->push_trace_data)) {
            $this->push_trace_data = stream_get_contents($this->push_trace_data);
        }
        $lines = [];
        $isFirstFound = false;
        foreach (explode("\n", $this->push_trace_data) as $line) {
            if (!$isFirstFound && strpos($line, \yii\queue\Queue::class)) {
                $isFirstFound = true;
            }
            if ($isFirstFound) {
                list(, $line) = explode(' ', trim($line), 2);
                $lines[] = $line;
            }
        }
        return $lines;
    }

    /**
     * @param array $values
     */
    public function setPushEnv($values)
    {
        ksort($values);
        $this->push_env_data = serialize($values);
    }

    /**
     * @return array
     */
    public function getPushEnv()
    {
        if ($this->push_env_data === null) {
            return [];
        }
        if (is_resource($this->push_env_data)) {
            $this->push_env_data = stream_get_contents($this->push_env_data);
        }
        return unserialize($this->push_env_data);
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
        if ($this->lastExec && $this->lastExec->isDone() && !$this->lastExec->retry) {
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
