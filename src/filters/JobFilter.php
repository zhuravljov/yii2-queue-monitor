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
    const IS_FAILED = 'failed';
    const IS_STOPPED = 'stopped';

    public $is;
    public $sender;
    public $class;
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
            [['is', 'sender', 'class', 'pushed'], 'trim'],
            ['is', 'string'],
            ['is', 'in', 'range' => array_keys($this->scopeList())],
            ['sender', 'string'],
            ['class', 'string'],
            ['pushed', 'string'],
            ['pushed', 'match', 'pattern' => '/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/'],
        ];
    }

    /**
     */
    public function storeParams()
    {
        $params = [];
        foreach ($this->attributes as $attribute => $value) {
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'is' => 'Scope',
            'sender' => 'Sender',
            'class' => 'Job',
            'pushed' => 'Pushed'
        ];
    }

    /**
     * @return array
     */
    public function scopeList()
    {
        return [
            self::IS_WAITING => 'Waiting',
            self::IS_IN_PROGRESS => 'In progress',
            self::IS_DONE => 'Done',
            self::IS_SUCCESS => 'Done successfully',
            self::IS_BURIED => 'Buried',
            self::IS_FAILED => 'Has failed attempts',
            self::IS_STOPPED => 'Stopped',
        ];
    }

    /**
     * @return array
     */
    public function senderList()
    {
        return $this->env->cache->getOrSet(__METHOD__, function () {
            return PushRecord::find()
                ->select('p.sender_name')
                ->groupBy('p.sender_name')
                ->orderBy('p.sender_name')
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
     * @return PushQuery
     */
    public function search()
    {
        $query = PushRecord::find();
        if ($this->hasErrors()) {
            return $query->andWhere('1 = 0');
        }

        $query->andFilterWhere(['p.sender_name' => $this->sender]);
        $query->andFilterWhere(['like', 'p.job_class', $this->class]);
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
        } elseif ($this->is == self::IS_FAILED) {
            $query->hasFails();
        } elseif ($this->is == self::IS_STOPPED) {
            $query->stopped();
        }

        return $query;
    }

    /**
     * @return array
     */
    public function searchClasses()
    {
        return $this->search()
            ->select(['name' => 'p.job_class', 'count' => 'COUNT(*)'])
            ->groupBy(['name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * @return array
     */
    public function searchSenders()
    {
        return $this->search()
            ->select(['name' => 'p.sender_name', 'count' => 'COUNT(*)'])
            ->groupBy(['name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }
}