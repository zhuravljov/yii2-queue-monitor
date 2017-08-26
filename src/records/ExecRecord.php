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
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class ExecRecord extends ActiveRecord
{
    private $_errorLine;
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
     * @return false|string first error line
     */
    public function getErrorLine()
    {
        if ($this->_errorLine === null) {
            $this->_errorLine = false;
            if ($this->error !== null) {
                $this->_errorLine = trim(explode("\n", $this->error, 2)[0]);
            }
        }
        return $this->_errorLine;
    }
}
