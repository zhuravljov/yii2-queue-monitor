<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\records;

use Yii;
use yii\db\ActiveRecord;
use zhuravljov\yii\queue\monitor\Env;

/**
 * Class PushRecord
 *
 * @property integer $id
 * @property string $sender
 * @property string $job_uid
 * @property string $job_class
 * @property resource $job_object
 * @property integer $ttr
 * @property integer $delay
 * @property integer $pushed_at
 * @property integer $first_exec_id
 * @property integer $last_exec_id
 *
 * @property ExecRecord[] $execs
 * @property ExecRecord|null $firstExec
 * @property ExecRecord|null $lastExec
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class PushRecord extends ActiveRecord
{
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
     * @return string
     */
    public function getStatus()
    {
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
}
