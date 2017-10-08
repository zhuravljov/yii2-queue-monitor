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
        <?= $this->render('_index-search', ['filter' => $filter]) ?>
    </div>
    <div class="col-lg-9 col-lg-pull-3">
        <?= Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' =>'nav nav-tabs'],
            'items' => [
                [
                    'label' => Html::icon('tasks') . ' Jobs',
                    'url' => ['job/index'] + JobFilter::restoreParams(),
                    'active' => $actionId === 'index',
                ],
            ],
        ]) ?>
        <?= $content ?>
    </div>
</div>
