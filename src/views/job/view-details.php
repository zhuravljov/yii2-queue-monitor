<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\widgets\DetailView;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Details';
?>
<div class="monitor-job-details">
    <?= DetailView::widget([
        'model' => $record,
        'attributes' => [
            'sender_name',
            'job_uid',
            'job_class',
            'ttr',
            'delay',
            'pushed_at:relativeTime',
            [
                'label' => 'Wait Time',
                'format' => 'duration',
                'value' => ($record->firstExec ? $record->firstExec->reserved_at : time()) - ($record->pushed_at + $record->delay),
            ],
            'status',
        ],
        'options' => ['class' => 'table'],
    ]) ?>
</div>
