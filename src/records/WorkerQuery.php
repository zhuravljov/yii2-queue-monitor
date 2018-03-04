<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\records;

use Yii;
use yii\db\ActiveQuery;
use zhuravljov\yii\queue\monitor\Env;

/**
 * Class WorkerQuery
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class WorkerQuery extends ActiveQuery
{
    /**
     * @param int $pid
     * @return $this
     */
    public function byPid($pid)
    {
        return $this->andWhere(['pid' => $pid]);
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['finished_at' => null]);
        /** @var Env $env */
        $env = Yii::$container->get(Env::class);
        if ($env->canListenWorkerLoop()) {
            $this->andWhere(['>', 'pinged_at', time() - $env->workerPingInterval - 5]);
        }
        return $this;
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
