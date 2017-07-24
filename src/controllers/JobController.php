<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\controllers;

use Yii;
use yii\filters\VerbFilter;
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
                ],
            ],
        ];
    }

    /**
     * Pushed jobs
     *
     * @return string
     */
    public function actionIndex()
    {
        $filter = new JobFilter();
        $filter->load(Yii::$app->request->queryParams) && $filter->validate();

        return $this->render('index', [
            'filter' => $filter,
        ]);
    }

    /**
     * Details of a job
     *
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Pushes job
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionPush($id)
    {
        $push = $this->findRecord($id);
        /** @var Queue $queue */
        $queue = Yii::$app->get($push->sender);
        $uid = $queue->push(unserialize($push->job_object));
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
        if ($record = PushRecord::findOne($id)) {
            return $record;
        } else {
            throw new NotFoundHttpException('Record not found.');
        }
    }
}
