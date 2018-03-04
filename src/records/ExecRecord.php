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
 * Class ExecRecord
 *
 * @property int $id
 * @property int $push_id
 * @property null|int $worker_id
 * @property int $attempt
 * @property int $reserved_at
 * @property null|int $done_at
 * @property null|string $error
 * @property null|bool $retry
 *
 * @property PushRecord $push
 * @property null|WorkerRecord $worker
 *
 * @property int $duration
 * @property false|string $errorMessage
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class ExecRecord extends ActiveRecord
{
    private $_errorMessage;

    /**
     * @inheritdoc
     * @return ExecQuery the active query used by this AR class.
     */
    public static function find()
    {
        return Yii::createObject(ExecQuery::class, [get_called_class()]);
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
        return Yii::$container->get(Env::class)->execTableName;
    }

    /**
     * @return PushQuery
     */
    public function getPush()
    {
        return $this->hasOne(PushRecord::class, ['id' => 'push_id']);
    }

    /**
     * @return WorkerQuery
     */
    public function getWorker()
    {
        return $this->hasOne(WorkerRecord::class, ['id' => 'worker_id']);
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        if ($this->done_at) {
            return $this->done_at - $this->reserved_at;
        }
        return time() - $this->reserved_at;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->done_at !== null;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return $this->error !== null;
    }

    /**
     * @return false|string first error line
     */
    public function getErrorMessage()
    {
        if ($this->_errorMessage === null) {
            $this->_errorMessage = false;
            if ($this->error !== null) {
                $this->_errorMessage = trim(explode("\n", $this->error, 2)[0]);
            }
        }
        return $this->_errorMessage;
    }
}
