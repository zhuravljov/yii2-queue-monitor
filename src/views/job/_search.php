<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\filters\JobFilter $filter
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
?>
<div class="monitor-job-search">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'enableClientValidation' => false,
    ]) ?>
    <div class="row">
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'status')->dropDownList($filter->statusList(), ['prompt' => '']) ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'sender') ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'uid') ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'class') ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'delay') ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'pushed') ?>
        </div>
    </div>
    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
</div>
