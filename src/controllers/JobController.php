<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\queue\Job;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\queue\monitor\records\PushRecord;
use yii\queue\Queue;

/**
 * Class JobController
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class JobController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'push' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Pushed jobs
     */
    public function actionIndex()
    {
        /** @var JobFilter $filter */
        $filter = Yii::createObject(JobFilter::class);
        $filter->load(Yii::$app->request->queryParams) && $filter->validate();
        JobFilter::storeParams($filter);

        return $this->render('index', [
            'filter' => $filter,
        ]);
    }

    /**
     * Job view
     */
    public function actionView($id)
    {
        return $this->redirect(['view-details', 'id' => $id]);
    }

    /**
     * Push details
     */
    public function actionViewDetails($id)
    {
        return $this->render('view-details', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Job object data
     */
    public function actionViewData($id)
    {
        return $this->render('view-data', [
            'record' => $record = $this->findRecord($id),
            'job' => unserialize($record->job_object),
        ]);
    }

    /**
     * Attempts
     */
    public function actionViewAttempts($id)
    {
        return $this->render('view-attempts', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Pushes job
     */
    public function actionPush($id)
    {
        $push = $this->findRecord($id);

        /** @var Queue $queue */
        $queue = Yii::$app->get($push->sender);
        if (!($queue instanceof Queue)) {
            throw new ForbiddenHttpException("$push->sender component not found.");
        }

        $job = unserialize($push->job_object);
        if (is_object($job) && !($job instanceof Job)) {
            throw new ForbiddenHttpException('Job object must be ' . Job::class);
        }

        $uid = $queue->push($job);
        $newPush = PushRecord::find()->byJob($push->sender, $uid)->one();

        return $this->redirect(['view', 'id' => $newPush->id]);
    }

    /**
     * @param int $id
     * @return PushRecord
     * @throws NotFoundHttpException
     */
    protected function findRecord($id)
    {
        if ($record = PushRecord::find()->byId($id)->one()) {
            return $record;
        } else {
            throw new NotFoundHttpException('Record not found.');
        }
    }
}
