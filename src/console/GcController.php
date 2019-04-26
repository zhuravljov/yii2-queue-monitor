<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\console;

use yii\console\Controller;
use zhuravljov\yii\queue\monitor\records\ExecRecord;
use zhuravljov\yii\queue\monitor\records\PushRecord;
use zhuravljov\yii\queue\monitor\records\WorkerRecord;

/**
 * Garbage Collector Commands of Queue Monitor.
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class GcController extends Controller
{
    /**
     * @var bool verbose mode.
     */
    public $silent = false;

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'silent',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            's' => 'silent',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($this->silent) {
            $this->interactive = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function stdout($string)
    {
        if ($this->silent) {
            return false;
        }
        return parent::stdout($string);
    }

    /**
     * Clear deprecated records.
     *
     * @param string $interval
     * @link https://www.php.net/manual/en/dateinterval.construct.php
     */
    public function actionClearDeprecated($interval)
    {
        $ids = PushRecord::find()
            ->deprecated($interval)
            ->done()
            ->select('push.id')
            ->asArray()->column();
        $count = count($ids);
        if ($count && $this->confirm("Do you want to delete $count records?")) {
            $count = PushRecord::getDb()->transaction(function () use ($ids) {
                ExecRecord::deleteAll(['push_id' => $ids]);
                return PushRecord::deleteAll(['id' => $ids]);
            });
            $this->stdout("$count records deleted.\n");
        }
    }

    /**
     * Clear all records.
     */
    public function actionClearAll()
    {
        if ($this->confirm('Are you sure?')) {
            $count = PushRecord::getDb()->transaction(function () {
                WorkerRecord::deleteAll();
                ExecRecord::deleteAll();
                return PushRecord::deleteAll();
            });
            $this->stdout("$count records deleted.\n");
        }
    }

    /**
     * Clear lost worker records.
     */
    public function actionClearWorkers()
    {
        $count = WorkerRecord::deleteAll();
        $this->stdout("$count records deleted.\n");
    }
}
