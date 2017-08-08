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
                    'stop' => ['post'],
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
        $record = $this->findRecord($id);

        /** @var Queue $queue */
        $queue = Yii::$app->get($record->sender, false);
        if (!($queue instanceof Queue)) {
            Yii::$app->session->setFlash('error', "The job isn't pushed because $record->sender component isn't found.");
            return $this->redirect(['view-details', 'id' => $record->id]);
        }

        $job = unserialize($record->job_object);
        if (gettype($job) === 'object' && !($job instanceof Job)) {
            Yii::$app->session->setFlash('error', 'The job isn\'t pushed because object must be ' . Job::class . '.');
            return $this->redirect(['view-data', 'id' => $record->id]);
        }

        $uid = $queue->push($job);
        $newRecord = PushRecord::find()->byJob($record->sender, $uid)->one();
        Yii::$app->session->setFlash('success', 'The job is pushed again.');

        return $this->redirect(['view', 'id' => $newRecord->id]);
    }

    /**
     * Stop
     */
    public function actionStop($id)
    {
        $record = $this->findRecord($id);
        if ($record->isStopped()) {
            Yii::$app->session->setFlash('error', 'The job is already stopped.');

            return $this->redirect(['view-details', 'id' => $record->id]);
        }
        if (!$record->canStop()) {
            Yii::$app->session->setFlash('error', 'The job is done.');

            return $this->redirect(['view-attempts', 'id' => $record->id]);
        }

        $record->stop();
        Yii::$app->session->setFlash('success', 'The job will be stopped.');

        return $this->redirect(['view-details', 'id' => $record->id]);
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
