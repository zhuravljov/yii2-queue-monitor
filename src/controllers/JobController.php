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
use zhuravljov\yii\queue\monitor\base\FlashTrait;
use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\queue\monitor\Module;
use zhuravljov\yii\queue\monitor\records\PushRecord;

/**
 * Class JobController
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class JobController extends Controller
{
    use FlashTrait;

    /**
     * @var Module
     */
    public $module;

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
    public function actionList()
    {
        return $this->render('list', [
            'filter' => $this->createFilter(),
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
            'record' => $this->findRecord($id),
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
     * Pushes a job again
     */
    public function actionPush($id)
    {
        if (!$this->module->canPushAgain) {
            throw new ForbiddenHttpException('Push is forbidden.');
        }

        $record = $this->findRecord($id);

        if (!$record->isSenderValid()) {
            return $this
                ->error("The job isn't pushed because $record->sender_name component isn't found.")
                ->redirect(['view-details', 'id' => $record->id]);
        }

        if (!$record->isJobValid()) {
            return $this
                ->error('The job isn\'t pushed because object must be ' . Job::class . '.')
                ->redirect(['view-data', 'id' => $record->id]);
        }

        $uid = $record->getSender()->push($record->getJob());
        $newRecord = PushRecord::find()->byJob($record->sender_name, $uid)->one();

        return $this
            ->success('The job is pushed again.')
            ->redirect(['view', 'id' => $newRecord->id]);
    }

    /**
     * Stop a job
     */
    public function actionStop($id)
    {
        if (!$this->module->canPushAgain) {
            throw new ForbiddenHttpException('Stop is forbidden.');
        }

        $record = $this->findRecord($id);

        if ($record->isStopped()) {
            return $this
                ->error('The job is already stopped.')
                ->redirect(['view-details', 'id' => $record->id]);
        }

        if (!$record->canStop()) {
            return $this
                ->error('The job is already done.')
                ->redirect(['view-attempts', 'id' => $record->id]);
        }

        $record->stop();

        return $this->success( 'The job will be stopped.')
            ->redirect(['view-details', 'id' => $record->id]);
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

    /**
     * @return JobFilter
     */
    protected function createFilter()
    {
        /** @var JobFilter $filter */
        $filter = Yii::createObject(JobFilter::class);
        $filter->load(Yii::$app->request->queryParams) && $filter->validate();
        $filter->storeParams();

        return $filter;
    }
}
