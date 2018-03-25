<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\data\ActiveDataProvider;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use zhuravljov\yii\queue\monitor\assets\JobItemAsset;
use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\queue\monitor\widgets\FilterBar;
use zhuravljov\yii\queue\monitor\widgets\LinkPager;

if (JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = ['label' => 'Jobs', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Filtered';
} else {
    $this->params['breadcrumbs'][] = 'Jobs';
}

JobItemAsset::register($this);
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
                'dataProvider' => new ActiveDataProvider([
                    'query' => $filter->search()
                        ->with(['parent', 'firstExec', 'lastExec', 'execTotal']),
                    'sort' => [
                        'defaultOrder' => [
                            'id' => SORT_DESC,
                        ],
                    ],
                ]),
                'pager' => [
                    'class' => LinkPager::class,
                ],
                'emptyText' => 'No jobs found.',
                'emptyTextOptions' => ['class' => 'empty lead'],
                'itemView' => '_index-item',
                'itemOptions' => ['tag' => null],
            ]) ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
