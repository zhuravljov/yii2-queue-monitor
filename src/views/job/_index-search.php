<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\filters\JobFilter $filter
 */

use yii\bootstrap\ActiveForm;
?>
<div class="monitor-job-search">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'enableClientValidation' => false,
    ]) ?>
    <div class="row">
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'is')->dropDownList($filter->statusList(), ['prompt' => '']) ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'sender')->textInput(['list' => 'job-sender']) ?>
            <?= $this->render('_data-list', ['id' => 'job-sender', 'values' => $filter->senderList()]) ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'uid') ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'class')->textInput(['list' => 'job-class']) ?>
            <?= $this->render('_data-list', ['id' => 'job-class', 'values' => $filter->classList()]) ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'delay') ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'pushed') ?>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">
        <span class="glyphicon glyphicon-search"></span>
        Search
    </button>
    <?php ActiveForm::end() ?>
</div>
