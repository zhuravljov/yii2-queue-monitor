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
    'label' => 'Job ' . $record->job_uid . ' by ' . $record->sender,
    'url' => ['view', 'id' => $record->id],
];
?>
<div class="pull-right">
    <?= Html::a(
        Html::icon('stop') . ' Stop',
        ['stop', 'id' => $record->id],
        [
            'title' => 'Mark as stopped.',
            'disabled' => !$record->canStop(),
            'data' => [
                'method' => 'post',
                'confirm' => 'Are you sure?',
            ],
            'class' => 'btn btn-' . ($record->canStop() ? 'danger' : 'default'),
        ]
    ) ?>
    <?= Html::a(
        Html::icon('repeat') . ' Push',
        ['push', 'id' => $record->id],
        [
            'title' => 'Push again.',
            'data' => [
                'method' => 'post',
                'confirm' => 'Are you sure?',
            ],
            'class' => 'btn btn-primary',
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
            'label' => strtr('Attempts (count)', [
                'count' => $record->last_exec_id ? $record->lastExec->attempt : 0,
            ]),
            'url' => ['job/view-attempts', 'id' => $record->id],
        ],
    ],
]) ?>