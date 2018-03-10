<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\widgets\DetailView;
use zhuravljov\yii\queue\monitor\Module;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Details';
?>
<div class="monitor-job-details">
    <?= DetailView::widget([
        'model' => $record,
        'formatter' => Module::getInstance()->formatter,
        'attributes' => [
            'sender_name',
            'job_uid',
            'job_class',
            'ttr',
            'delay',
            'pushed_at:relativeTime',
            'waitTime:duration',
            'status',
        ],
        'options' => ['class' => 'table'],
    ]) ?>
</div>
