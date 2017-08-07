<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\filters;

use DateTime;
use Yii;
use yii\base\Model;
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
    const IS_WAITING = 'waiting';
    const IS_IN_PROGRESS = 'in-progress';
    const IS_DONE = 'done';
    const IS_SUCCESS = 'success';
    const IS_BURIED  = 'buried';
    const IS_HAVE_FAILS = 'have-fails';

    public $is;
    public $sender;
    public $uid;
    public $class;
    public $delay;
    public $pushed;

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
            [['is', 'sender', 'uid', 'class', 'delay', 'pushed'], 'trim'],
            ['is', 'string'],
            ['is', 'in', 'range' => array_keys($this->statusList())],
            ['sender', 'string'],
            ['uid', 'string'],
            ['class', 'string'],
            ['delay', 'string'],
            ['pushed', 'string'],
            ['pushed', 'match', 'pattern' => '/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/'],
        ];
    }

    /**
     * @return PushQuery
     */
    public function search()
    {
        $query = PushRecord::find()->with('lastExec');
        if ($this->hasErrors()) {
            return $query;
        }

        $query->andFilterWhere(['p.sender' => $this->sender]);
        $query->andFilterWhere(['p.job_uid' => $this->uid]);
        $query->andFilterWhere(['like', 'p.job_class', $this->class]);
        $query->andFilterCompare('p.delay', $this->delay);
        $this->filterDateRange($query, 'p.pushed_at', $this->pushed);

        if ($this->is == self::IS_WAITING) {
            $query->waiting();
        } elseif ($this->is == self::IS_IN_PROGRESS) {
            $query->inProgress();
        } elseif ($this->is == self::IS_DONE) {
            $query->done();
        } elseif ($this->is == self::IS_SUCCESS) {
            $query->success();
        } elseif ($this->is == self::IS_BURIED) {
            $query->buried();
        } elseif ($this->is == self::IS_HAVE_FAILS) {
            $query->hasFails();
        }

        return $query;
    }

    /**
     * @param PushQuery $query
     * @param string $name
     * @param string $value
     */
    private function filterDateRange(PushQuery $query, $name, $value)
    {
        $limits = explode(' - ', $value, 2);
        if (count($limits) === 2) {
            $begin = DateTime::createFromFormat('Y-m-d', $limits[0]);
            $end = DateTime::createFromFormat('Y-m-d', $limits[1]);
            if ($begin && $end) {
                $begin->setTime(0, 0, 0);
                $end->setTime(23, 59, 59);
                $query->andWhere(['between', $name, $begin->getTimestamp(), $end->getTimestamp()]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'is' => 'Status',
            'sender' => 'Sender Name',
            'uid' => 'Job Unique ID',
            'class' => 'Job Class',
            'delay' => 'Delay',
            'pushed' => 'Pushed Between'
        ];
    }

    /**
     * @return array
     */
    public function statusList()
    {
        return [
            self::IS_WAITING => 'Waiting',
            self::IS_IN_PROGRESS => 'In progress',
            self::IS_DONE => 'Done',
            self::IS_SUCCESS => 'Done successfully',
            self::IS_BURIED => 'Buried',
            self::IS_HAVE_FAILS => 'Have failed attempts',
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