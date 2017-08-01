<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\bootstrap\Html;
use yii\grid\GridView;
use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\queue\monitor\records\PushRecord;

$this->params['breadcrumbs'][]  = ['label' => 'Jobs', 'url' => ['index']];
if ($filtered = JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][]  = ['label' => 'Filtered', 'url' => ['index'] + $filtered];
}
?>
<div class="monitor-job-index">
    <div class="row">
        <div class="col-lg-3 col-lg-push-9">
            <?= $this->render('_search', [
                'filter' => $filter,
            ]) ?>
        </div>
        <div class="col-lg-9 col-lg-pull-3">
            <?= GridView::widget([
                'dataProvider' => $filter->search(),
                'columns' => [
                    'sender',
                    'job_uid',
                    'job_class',
                    'delay',
                    'pushed_at:relativeTime',
                    'status',
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{view}'
                    ],
                ],
                'tableOptions' => ['class' => 'table'],
                'rowOptions' => function (PushRecord $push) {
                    $options = [];
                    switch ($push->getStatus()) {
                        case PushRecord::STATUS_WAITING:
                        case PushRecord::STATUS_STARTED:
                        case PushRecord::STATUS_RESTARTED:
                            Html::addCssClass($options, 'active');
                            break;
                        case PushRecord::STATUS_FAILED:
                        case PushRecord::STATUS_BURIED:
                            Html::addCssClass($options, 'warning text-warning');
                            break;
                    }
                    return $options;
                }
            ]) ?>
        </div>
    </div>
</div>
