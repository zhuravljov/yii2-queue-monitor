<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\filters;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use zhuravljov\yii\queue\monitor\Env;
use zhuravljov\yii\queue\monitor\records\PushQuery;
use zhuravljov\yii\queue\monitor\records\PushRecord;

/**
 * Class JobFilter
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class JobFilter extends Model
{
    const STATUS_WAITING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_DONE = 3;
    const STATUS_SUCCESS = 4;
    const STATUS_BURIED  = 5;
    const STATUS_HAS_FAILS = 6;

    public $sender;
    public $uid;
    public $class;
    public $delay;
    public $pushed;
    public $status;

    /**
     * @var Env
     */
    private $env;

    /**
     * @param Env $env
     * @param array $config
     */
    public function __construct(Env $env, $config = [])
    {
        $this->env = $env;
        parent::__construct($config);
    }

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['sender', 'uid', 'class', 'delay', 'pushed', 'status'], 'safe'],
        ];
    }

    /**
     * @return PushQuery
     */
    public function search()
    {
        $query = PushRecord::find()->with('lastExec');

        $query->andFilterWhere(['p.sender' => $this->sender]);
        $query->andFilterWhere(['p.job_uid' => $this->uid]);
        $query->andFilterWhere(['like', 'p.job_class', $this->class]);
        $query->andFilterCompare('p.delay', $this->delay);

        if ($this->status == self::STATUS_WAITING) {
            $query->waiting();
        } elseif ($this->status == self::STATUS_IN_PROGRESS) {
            $query->inProgress();
        } elseif ($this->status == self::STATUS_DONE) {
            $query->done();
        } elseif ($this->status == self::STATUS_SUCCESS) {
            $query->success();
        } elseif ($this->status == self::STATUS_BURIED) {
            $query->buried();
        } elseif ($this->status == self::STATUS_HAS_FAILS) {
            $query->hasFails();
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);
    }

    /**
     * @return array
     */
    public function statusList()
    {
        return [
            self::STATUS_WAITING => 'Waiting',
            self::STATUS_IN_PROGRESS => 'In progress',
            self::STATUS_DONE => 'Done',
            self::STATUS_SUCCESS => 'Done successfully',
            self::STATUS_BURIED => 'Buried',
            self::STATUS_HAS_FAILS => 'Has failed attempts',
        ];
    }

    /**
     * @return array
     */
    public function senderList()
    {
        return $this->env->cache->getOrSet(__METHOD__, function () {
            return PushRecord::find()
                ->select('p.sender')
                ->groupBy('p.sender')
                ->orderBy('p.sender')
                ->column();
        }, 3600);
    }

    /**
     * @return array
     */
    public function classList()
    {
        return $this->env->cache->getOrSet(__METHOD__, function () {
            return PushRecord::find()
                ->select('p.job_class')
                ->groupBy('p.job_class')
                ->orderBy('p.job_class')
                ->column();
        }, 3600);
    }

    /**
     * @param JobFilter $filter
     */
    public static function storeParams(JobFilter $filter)
    {
        $params = [];
        foreach ($filter->attributes as $attribute => $value) {
            if ($value !== null && $value !== '') {
                $params[$attribute] = $value;
            }
        }
        Yii::$app->session->set(JobFilter::class, $params);
    }

    /**
     * @return array
     */
    public static function restoreParams()
    {
        return Yii::$app->session->get(JobFilter::class, []);
    }
}