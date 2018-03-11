<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use zhuravljov\yii\queue\monitor\assets\JobIndexAsset;
use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\queue\monitor\records\PushRecord;
use zhuravljov\yii\queue\monitor\widgets\FilterBar;
use zhuravljov\yii\queue\monitor\widgets\LinkPager;

if (JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = ['label' => 'Jobs', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Filtered';
} else {
    $this->params['breadcrumbs'][] = 'Jobs';
}

JobIndexAsset::register($this);
?>
<div class="monitor-job-index">
    <div class="row">
        <div class="col-lg-3 col-lg-push-9">
            <?php FilterBar::begin() ?>
            <?= $this->render('_job-filter', compact('filter')) ?>
            <?php FilterBar::end() ?>
        </div>
        <div class="col-lg-9 col-lg-pull-3">
            <?php Pjax::begin() ?>
            <?= ListView::widget([
                'pager' => [
                    'class' => LinkPager::class,
                ],
                'itemView' => '_index-item',
                'itemOptions' => function (PushRecord $push) {
                    $options = ['class' => 'job-item'];
                    switch ($push->getStatus()) {
                        case PushRecord::STATUS_STOPPED:
                            Html::addCssClass($options, 'bg-info');
                            break;
                        case PushRecord::STATUS_WAITING:
                        case PushRecord::STATUS_STARTED:
                            Html::addCssClass($options, 'bg-success');
                            break;
                        case PushRecord::STATUS_FAILED:
                        case PushRecord::STATUS_RESTARTED:
                            Html::addCssClass($options, 'bg-warning');
                            break;
                        case PushRecord::STATUS_BURIED:
                            Html::addCssClass($options, 'bg-danger');
                            break;
                        default:
                            Html::addCssClass($options, 'bg-default');
                    }
                    return $options;
                },
                'dataProvider' => new ActiveDataProvider([
                    'query' => $filter->search()
                        ->with(['parent', 'firstExec', 'lastExec', 'execTotal']),
                    'sort' => [
                        'defaultOrder' => [
                            'id' => SORT_DESC,
                        ],
                    ],
                ]),
            ]) ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
