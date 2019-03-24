<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\records;

use yii\db\ActiveQuery;
use zhuravljov\yii\queue\monitor\Env;

/**
 * Worker Query
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class WorkerQuery extends ActiveQuery
{
    /**
     * @var Env
     */
    private $env;

    /**
     * @param string $modelClass
     * @param Env $env
     * @param array $config
     * @inheritdoc
     */
    public function __construct($modelClass, Env $env, array $config = [])
    {
        $this->env = $env;
        parent::__construct($modelClass, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->alias('worker');
    }

    /**
     * @param string $host
     * @param int $pid
     * @return $this
     */
    public function byEvent($host, $pid)
    {
        return $this->andWhere([
            'worker.host' => $host,
            'worker.pid' => $pid,
        ]);
    }

    /**
     * @return $this
     */
    public function active()
    {
        return $this
            ->andWhere(['worker.finished_at' => null])
            ->leftJoin(['exec' => ExecRecord::tableName()], '{{exec}}.[[id]] = {{worker}}.[[last_exec_id]]')
            ->leftJoin(['push' => PushRecord::tableName()], '{{push}}.[[id]] = {{exec}}.[[push_id]]')
            ->andWhere([
                'or',
                ['>', 'worker.pinged_at', time() - $this->env->workerPingInterval - 5],
                [
                    'and',
                    ['is not', 'worker.last_exec_id', null],
                    ['exec.finished_at' => null],
                ],
            ]);
    }

    /**
     * @inheritdoc
     * @return WorkerRecord[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return WorkerRecord|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
