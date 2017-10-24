<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 * @var string $content
 */

use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use zhuravljov\yii\queue\monitor\filters\JobFilter;

$actionId = Yii::$app->controller->action->id;
?>
<div class="row">
    <div class="col-lg-3 col-lg-push-9">
        <?= $this->render('_index-filter', ['filter' => $filter]) ?>
    </div>
    <div class="col-lg-9 col-lg-pull-3">
        <?= Nav::widget([
            'encodeLabels' => false,
            'options' => [
                'class' =>'nav nav-tabs',
                'style' => 'margin-bottom: 20px',
            ],
            'items' => [
                [
                    'label' => Html::icon('stats') . ' Stats',
                    'url' => ['job/stats'] + JobFilter::restoreParams(),
                    'active' => $actionId === 'stats',
                ],
                [
                    'label' => Html::icon('tasks') . ' Jobs',
                    'url' => ['job/list'] + JobFilter::restoreParams(),
                    'active' => $actionId === 'list',
                ],
            ],
        ]) ?>
        <?= $content ?>
    </div>
</div>
