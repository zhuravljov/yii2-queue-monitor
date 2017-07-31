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
    <?= $form->field($filter, 'status')->dropDownList($filter->statusList(), ['prompt' => '']) ?>
    <?= $form->field($filter, 'sender') ?>
    <?= $form->field($filter, 'uid') ?>
    <?= $form->field($filter, 'class') ?>
    <?= $form->field($filter, 'delay') ?>
    <?= $form->field($filter, 'pushed') ?>
    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
</div>
