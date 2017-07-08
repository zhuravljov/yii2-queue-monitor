<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\records;

use yii\db\ActiveQuery;

/**
 * Class PushQuery
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class PushQuery extends ActiveQuery
{
    /**
     * @param string $sender
     * @param string $uid
     * @return $this
     */
    public function byJob($sender, $uid)
    {
        return $this
            ->andWhere(['sender' => $sender])
            ->andWhere(['job_uid' => $uid])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1);
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
