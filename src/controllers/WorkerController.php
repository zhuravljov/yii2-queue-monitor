<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\controllers;

use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use zhuravljov\yii\queue\monitor\base\FlashTrait;
use zhuravljov\yii\queue\monitor\filters\WorkerFilter;
use zhuravljov\yii\queue\monitor\Module;
use zhuravljov\yii\queue\monitor\records\WorkerRecord;

/**
 * Worker Controller
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class WorkerController extends Controller
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
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'stop' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Worker List
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'filter' => WorkerFilter::ensure(),
        ]);
    }

    /**
     * Stops a worker
     *
     * @param int $id
     * @throws ForbiddenHttpException
     * @return \yii\web\Response
     */
    public function actionStop($id)
    {
        if (!$this->module->canWorkerStop) {
            throw new ForbiddenHttpException('Stop is forbidden.');
        }

        $this->findRecord($id)->stop();
        return $this->success('Worker was stopped.')
            ->redirect(['index']);
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @return WorkerRecord
     */
    protected function findRecord($id)
    {
        if ($record = WorkerRecord::findOne($id)) {
            return $record;
        }
        throw new NotFoundHttpException('Record not found.');
    }
}
