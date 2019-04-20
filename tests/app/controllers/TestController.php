<?php

namespace tests\app\controllers;

use tests\app\jobs\RecursionJob;
use tests\app\jobs\SimpleJob;
use Yii;
use yii\web\Controller;
use zhuravljov\yii\queue\monitor\base\FlashTrait;

class TestController extends Controller
{
    use FlashTrait;

    /**
     * Home page with redirecting to a base page of the module.
     */
    public function actionIndex()
    {
        return $this->redirect(['/qmonitor']);
    }

    public function actionPushSimpleJob()
    {
        $this->getQueue()->push(new SimpleJob());
        return $this->success('A simple job has been pushed.')->goBackward();
    }

    public function actionPushRecursionJob()
    {
        $this->getQueue()->push(new RecursionJob());
        return $this->success('A recursion job has been pushed.')->goBackward();
    }

    public function actionPushManyRecursionJobs($count = 10)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->getQueue()->push(new RecursionJob());
        }
        return $this->success("$count recursion jobs have been pushed.")->goBackward();
    }

    /**
     * @return \yii\queue\Queue
     */
    protected function getQueue()
    {
        return Yii::$app->queue;
    }

    /**
     * @return \yii\web\Response
     */
    protected function goBackward()
    {
        return $this->redirect(Yii::$app->request->getReferrer() ?: Yii::$app->getHomeUrl());
    }
}
