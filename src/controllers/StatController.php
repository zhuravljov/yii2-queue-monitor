<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\controllers;

use yii\web\Controller;
use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\queue\monitor\Module;

/**
 * Class StatController
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class StatController extends Controller
{
    /**
     * @var Module
     */
    public $module;

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'filter' => JobFilter::build(),
        ]);
    }
}