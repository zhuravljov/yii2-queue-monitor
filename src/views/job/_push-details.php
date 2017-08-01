<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\widgets\DetailView;
?>
<div class="monitor-job-push-details">
    <?= DetailView::widget([
        'model' => $record,
        'attributes' => [
            'sender',
            'job_uid',
            'job_class',
            'ttr',
            'delay',
            'pushed_at:relativeTime',
            [
                'label' => 'Wait Time',
                'format' => 'raw',
                'value' => Yii::$app->formatter->asRelativeTime(
                    $record->firstExec ? $record->firstExec->reserved_at : null,
                    $record->pushed_at + $record->delay
                ),
            ],
            'status',
        ],
        'options' => ['class' => 'table'],
    ]) ?>
</div>
