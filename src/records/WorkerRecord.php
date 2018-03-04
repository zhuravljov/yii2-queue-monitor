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
 * Class WorkerRecord
 *
 * @property int $id
 * @property string $sender_name
 * @property int $pid
 * @property int $started_at
 * @property int $pinged_at
 * @property null|int $finished_at
 * @property null|int $last_exec_id
 *
 * @property null|ExecRecord $lastExec
 * @property ExecRecord[] $execs
 * @property array $execTotal
 *
 * @property int $execStartedCount
 * @property int $execDoneCount
 * @property int $duration
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class WorkerRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     * @return WorkerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return Yii::createObject(WorkerQuery::class, [get_called_class()]);
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
        return Yii::$container->get(Env::class)->workerTableName;
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
    public function getExecs()
    {
        return $this->hasMany(ExecRecord::class, ['worker_id' => 'id']);
    }

    /**
     * @return ExecQuery
     */
    public function getExecTotal()
    {
        return $this->hasOne(ExecRecord::class, ['worker_id' => 'id'])
            ->select([
                'worker_id',
                'started' => 'COUNT(*)',
                'done' => 'COUNT(done_at)',
            ])
            ->groupBy('worker_id')
            ->asArray();
    }

    /**
     * @return int
     */
    public function getExecStartedCount()
    {
        return $this->execTotal['started'] ?: 0;
    }

    /**
     * @return int
     */
    public function getExecDoneCount()
    {
        return $this->execTotal['done'] ?: 0;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        if ($this->finished_at) {
            return $this->finished_at - $this->started_at;
        }
        return time() - $this->started_at;
    }
}
