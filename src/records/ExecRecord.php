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
 * @property integer $id
 * @property integer $push_id
 * @property integer $attempt
 * @property integer $reserved_at
 * @property null|integer $done_at
 * @property null|string $error
 * @property null|integer $retry
 *
 * @property PushRecord $push
 *
 * @property int $duration
 * @property false|string $errorMessage
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class ExecRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     * @return ExecQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExecQuery(get_called_class());
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
     * @return int
     */
    public function getDuration()
    {
        if ($this->done_at) {
            return $this->done_at - $this->reserved_at;
        } else {
            return time() - $this->reserved_at;
        }
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

    private $_errorMessage;
}
