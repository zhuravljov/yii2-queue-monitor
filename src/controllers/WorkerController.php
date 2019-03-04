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
use zhuravljov\yii\queue\monitor\Env;
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
     * @var Env
     */
    protected $env;

    public function __construct($id, $module, Env $env, array $config = [])
    {
        $this->env = $env;
        parent::__construct($id, $module, $config);
    }

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
            throw new ForbiddenHttpException(Module::t('notice', 'Stop is forbidden.'));
        }

        $record = $this->findRecord($id);
        $record->stop();
        return $this
            ->success(Module::t('notice', 'The worker will be stopped within {timeout} sec.', [
                'timeout' => $record->pinged_at + $this->env->workerPingInterval - time(),
            ]))
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
        throw new NotFoundHttpException(Module::t('notice', 'Record not found.'));
    }
}
