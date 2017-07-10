<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->params['breadcrumbs'][] = ['label' => 'Jobs', 'url' => ['index']];
$this->params['breadcrumbs'][]  = '#' . $record->id;
?>
<div class="monitor-job-view">
    <p>
        <?= Html::a('Push again', ['push', 'id' => $record->id], [
            'class' => 'btn btn-default',
            'data' => ['method' => 'post', 'confirm' => 'Are you sure?'],
        ]) ?>
    </p>
    <div class="row">
        <div class="col-lg-5">
            <?= DetailView::widget([
                'model' => $record,
                'attributes' => [
                    'sender',
                    'job_uid',
                    'job_class',
                    'ttr',
                    'delay',
                    'pushed_at:relativeTime',
                    'status',
                ],
            ]) ?>
        </div>
        <div class="col-lg-7">
            <div class="well">
                <?= \yii\helpers\VarDumper::dumpAsString(unserialize($record->job_object), 10, true) ?>
            </div>
        </div>
    </div>
</div>
