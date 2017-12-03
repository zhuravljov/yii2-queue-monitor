<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use zhuravljov\yii\queue\monitor\filters\JobFilter;

$this->params['breadcrumbs'][] = [
    'label' => 'Jobs',
    'url' => ['index'],
];
if ($filtered = JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = [
        'label' => 'Filtered',
        'url' => ['index'] + $filtered,
    ];
}
$this->params['breadcrumbs'][]  = [
    'label' => 'Job ' . $record->job_uid . ' by ' . $record->sender_name,
    'url' => ['view', 'id' => $record->id],
];

/** @var \zhuravljov\yii\queue\monitor\Module $module */
$module = Yii::$app->controller->module;
?>
<div class="pull-right">
    <?= !$module->canStop ? '' : Html::a(
        Html::icon('stop') . ' Stop',
        ['stop', 'id' => $record->id],
        [
            'title' => 'Mark as stopped.',
            'data' => [
                'method' => 'post',
                'confirm' => 'Are you sure?',
            ],
            'disabled' => !$record->canStop(),
            'class' => 'btn btn-' . ($record->canStop() ? 'danger' : 'default'),
        ]
    ) ?>
    <?= !$module->canPushAgain ? '' : Html::a(
        Html::icon('repeat') . ' Push Again',
        ['push', 'id' => $record->id],
        [
            'title' => 'Push again.',
            'data' => [
                'method' => 'post',
                'confirm' => 'Are you sure?',
            ],
            'disabled' => !$record->canPushAgain(),
            'class' => 'btn btn-' . ($record->canPushAgain() ? 'primary' : 'default'),
        ]
    ) ?>
</div>
<?= Nav::widget([
    'options' => ['class' =>'nav nav-tabs'],
    'items' => [
        [
            'label' => 'Details',
            'url' => ['job/view-details', 'id' => $record->id],
        ],
        [
            'label' => 'Data',
            'url' => ['job/view-data', 'id' => $record->id],
        ],
        [
            'label' => "Attempts ($record->attemptCount)",
            'url' => ['job/view-attempts', 'id' => $record->id],
        ],
    ],
]) ?>