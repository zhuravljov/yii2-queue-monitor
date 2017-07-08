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
    <?= DetailView::widget([
        'model' => $record,
    ]) ?>
</div>
