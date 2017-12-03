<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 * @var string $content
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\helpers\Url;
use yii\web\JsExpression;
use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\widgets\DateRangePicker;

?>
<div class="row">
    <div class="col-lg-3 col-lg-push-9">
        <div class="monitor-filter">
            <?php $form = ActiveForm::begin([
                'id' => 'job-filter',
                'method' => 'get',
                'action' => ['/' . Yii::$app->controller->route],
                'enableClientValidation' => false,
            ]) ?>
            <div class="row">
                <div class="col-lg-12 col-md-3 col-sm-6">
                    <?= $form->field($filter, 'is')->dropDownList($filter->scopeList(), ['prompt' => '']) ?>
                </div>
                <div class="col-lg-12 col-md-3 col-sm-6">
                    <?= $form->field($filter, 'sender')->textInput(['list' => 'job-sender']) ?>
                    <?= $this->render('_data-list', ['id' => 'job-sender', 'values' => $filter->senderList()]) ?>
                </div>
                <div class="col-lg-12 col-md-3 col-sm-6">
                    <?= $form->field($filter, 'class')->textInput(['list' => 'job-class']) ?>
                    <?= $this->render('_data-list', ['id' => 'job-class', 'values' => $filter->classList()]) ?>
                </div>
                <div class="col-lg-12 col-md-3 col-sm-6">
                    <?= $form->field($filter, 'pushed')->widget(DateRangePicker::class, [
                        'clientOptions' => [
                            'opens' => 'left',
                            'autoUpdateInput' => false,
                            'alwaysShowCalendars' => true,
                            'ranges' => [
                                'Today' => [
                                    new JsExpression('moment()'),
                                    new JsExpression('moment()'),
                                ],
                                'Yesterday' => [
                                    new JsExpression('moment().subtract(1, "days")'),
                                    new JsExpression('moment().subtract(1, "days")'),
                                ],
                                'Last Week' => [
                                    new JsExpression('moment().subtract(6, "days")'),
                                    new JsExpression('moment()'),
                                ],
                            ],
                            'locale' => [
                                'format' => 'YYYY-MM-DD',
                                'separator' => ' - ',
                                'cancelLabel' => 'For all time',
                            ],
                        ],
                        'clientEvents' => [
                            'apply.daterangepicker' => 'function (ev, picker) { $(this).val(picker.startDate.format(picker.locale.format) + picker.locale.separator + picker.endDate.format(picker.locale.format)); }',
                            'cancel.daterangepicker' => 'function (ev, picker) { $(this).val(""); }',
                        ],
                    ]) ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-search"></span>
                Search
            </button>
            <?php if (JobFilter::restoreParams()): ?>
                <a href="<?= Url::to(['/' . Yii::$app->controller->route]) ?>" class="btn btn-default">
                    Reset
                </a>
            <?php endif; ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>
    <div class="col-lg-9 col-lg-pull-3">
        <div class="monitor-nav">
            <?= Nav::widget([
                'encodeLabels' => false,
                'options' => [
                    'class' =>'nav nav-tabs',
                    'style' => 'margin-bottom: 20px',
                ],
                'items' => [
                    [
                        'label' => Html::icon('stats') . ' Stats',
                        'url' => ['stat/index'] + JobFilter::restoreParams(),
                        'active' => Yii::$app->controller->id === 'stat',
                    ],
                    [
                        'label' => Html::icon('tasks') . ' Jobs',
                        'url' => ['job/index'] + JobFilter::restoreParams(),
                        'active' => Yii::$app->controller->id === 'job',
                    ],
                ],
            ]) ?>
            <?= $content ?>
        </div>
    </div>
</div>