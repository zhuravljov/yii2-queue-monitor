<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\widgets\DetailView;

$this->params['breadcrumbs'][] = ['label' => 'Jobs', 'url' => ['index']];
$this->params['breadcrumbs'][]  = '#' . $record->id;
?>
<div class="monitor-job-view">
    <?= DetailView::widget([
        'model' => $record,
    ]) ?>
</div>
