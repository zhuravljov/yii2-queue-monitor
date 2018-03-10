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
            'sender_name:text:Sender',
            'job_uid:text:Job UID',
            'job_class:text:Class',
            'push_ttr:integer:Push TTR',
            'push_delay:integer:Delay',
            'pushed_at:relativeTime:Pushed',
            'waitTime:duration',
            'status:text',
        ],
        'options' => ['class' => 'table table-hover'],
    ]) ?>
</div>
