<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\filters;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use zhuravljov\yii\queue\monitor\records\PushRecord;

/**
 * Class JobFilter
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class JobFilter extends Model
{
    public $sender;
    public $job_uid;
    public $job_class;
    public $delay;

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['sender', 'job_uid', 'job_class', 'delay'], 'safe'],
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = PushRecord::find()->with('lastExec');
        $query->andFilterWhere(['sender' => $this->sender]);
        $query->andFilterWhere(['job_uid' => $this->job_uid]);
        $query->andFilterWhere(['like', 'job_class', $this->job_class]);
        $query->andFilterCompare('delay', $this->delay);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'sender',
                    'job_uid',
                    'job_class',
                    'delay',
                    'pushed_at',
                ],
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);
    }
}