<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\records;

use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\queue\JobInterface;
use yii\queue\Queue;
use zhuravljov\yii\queue\monitor\Env;
use zhuravljov\yii\queue\monitor\Module;

/**
 * Push Record
 *
 * @property int $id
 * @property null|int $parent_id
 * @property string $sender_name
 * @property string $job_uid
 * @property string $job_class
 * @property string|resource $job_data
 * @property int $ttr
 * @property int $delay
 * @property string|null $trace
 * @property string|null $context
 * @property int $pushed_at
 * @property int|null $stopped_at
 * @property int|null $first_exec_id
 * @property int|null $last_exec_id
 *
 * @property PushRecord $parent
 * @property PushRecord[] $children
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
 * @property array $jobParams
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
        return Env::ensure()->db;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Env::ensure()->pushTableName;
    }

    /**
     * @return PushQuery
     */
    public function getParent()
    {
        return $this->hasOne(static::class, ['id' => 'parent_id']);
    }

    /**
     * @return PushQuery
     */
    public function getChildren()
    {
        return $this->hasMany(static::class, ['parent_id' => 'id']);
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
                'exec.push_id',
                'attempts' => 'COUNT(*)',
                'errors' => 'COUNT(exec.error)',
            ])
            ->groupBy('exec.push_id')
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
            return $this->firstExec->started_at - $this->pushed_at - $this->delay;
        }
        return time() - $this->pushed_at - $this->delay;
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
    
    public function getStatusLabel($label)
    {
        $labels = [
            self::STATUS_STOPPED => Module::t('main', 'Stopped'),
            self::STATUS_BURIED => Module::t('main', 'Buried'),
            self::STATUS_DONE => Module::t('main', 'Done'),
            self::STATUS_FAILED => Module::t('main', 'Failed'),
            self::STATUS_RESTARTED => Module::t('main', 'Restarted'),
            self::STATUS_STARTED => Module::t('main', 'Started'),
            self::STATUS_WAITING => Module::t('main', 'Waiting'),
        ];
        if (!isset($labels[$label])) {
            throw new InvalidArgumentException('label not found');
        }
        return $labels[$label];
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
        $data = [];
        foreach (get_object_vars($job) as $name => $value) {
            $data[$name] = $this->serializeData($value);
        }
        $this->job_data = Json::encode($data);
    }

    /**
     * @return array of job properties
     */
    public function getJobParams()
    {
        if (is_resource($this->job_data)) {
            $this->job_data = stream_get_contents($this->job_data);
        }
        $params = [];
        foreach (Json::decode($this->job_data) as $name => $value) {
            $params[$name] = $this->unserializeData($value);
        }
        return $params;
    }

    /**
     * @return bool
     */
    public function isJobValid()
    {
        return is_subclass_of($this->job_class, JobInterface::class);
    }

    /**
     * @return JobInterface|mixed
     */
    public function createJob()
    {
        return Yii::createObject(['class' => $this->job_class] + $this->getJobParams());
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

    /**
     * @param mixed $data
     * @return mixed
     */
    private function serializeData($data)
    {
        if (is_object($data)) {
            $result = ['=class=' => get_class($data)];
            foreach (get_object_vars($data) as $name => $value) {
                $result[$name] = $this->serializeData($value);
            }
            return $result;
        }

        if (is_array($data)) {
            $result = [];
            foreach ($data as $name => $value) {
                $result[$name] = $this->serializeData($value);
            }
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    private function unserializeData($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        if (!isset($data['=class='])) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->unserializeData($value);
            }
            return $result;
        }

        $config = ['class' => $data['=class=']];
        unset($data['=class=']);
        foreach ($data as $property => $value) {
            $config[$property] = $this->unserializeData($value);
        }
        return Yii::createObject($config);
    }
}
