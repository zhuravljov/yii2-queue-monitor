<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\records;

use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * Class PushQuery
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class PushQuery extends ActiveQuery
{
    public function init()
    {
        parent::init();
        $this->alias('p');
    }

    public function byId($id)
    {
        return $this->andWhere(['p.id' => $id]);
    }

    /**
     * @param string $sender
     * @param string $uid
     * @return $this
     */
    public function byJob($sender, $uid)
    {
        return $this
            ->andWhere(['p.sender' => $sender])
            ->andWhere(['p.job_uid' => $uid])
            ->orderBy(['p.id' => SORT_DESC])
            ->limit(1);
    }

    /**
     * @return $this
     */
    public function waiting()
    {
        return $this
            ->joinLastExec()
            ->andWhere([
                'or',
                ['p.last_exec_id' => null],
                ['le.retry' => true],
            ]);
    }

    /**
     * @return $this
     */
    public function inProgress()
    {
        return $this
            ->joinLastExec()
            ->andWhere(['le.done_at' => null]);
    }

    /**
     * @return $this
     */
    public function done()
    {
        return $this
            ->joinLastExec()
            ->andWhere(['is not', 'le.done_at', null])
            ->andWhere(['le.retry' => false]);
    }

    /**
     * @return $this
     */
    public function success()
    {
        return $this
            ->done()
            ->andWhere(['le.error' => null]);
    }

    /**
     * @return $this
     */
    public function buried()
    {
        return $this
            ->done()
            ->andWhere(['is not', 'le.error', null]);
    }

    /**
     * @return $this
     */
    public function hasFails()
    {
        return $this
            ->andWhere(['exists', new Query([
                'from' => ['e' => ExecRecord::tableName()],
                'where' => '{{e}}.[[push_id]] = {{p}}.[[id]] AND {{e}}.[[error]] IS NOT NULL',
            ])]);
    }

    /**
     * @return $this
     */
    public function stopped()
    {
        return $this->andWhere(['is not', 'p.stopped_at', null]);
    }

    /**
     * @return $this
     */
    public function joinFirstExec()
    {
        return $this->leftJoin(
            ['fe' => ExecRecord::tableName()],
            '{{fe}}.[[id]] = {{p}}.[[first_exec_id]]'
        );
    }

    /**
     * @return $this
     */
    public function joinLastExec()
    {
        return $this->leftJoin(
            ['le' => ExecRecord::tableName()],
            '{{le}}.[[id]] = {{p}}.[[last_exec_id]]'
        );
    }

    /**
     * @inheritdoc
     * @return PushRecord[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PushRecord|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
