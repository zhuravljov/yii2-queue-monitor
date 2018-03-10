<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\queue\monitor\widgets\FilterBar;

if ($filterParams = JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = ['label' => 'Stats', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Filtered';
} else {
    $this->params['breadcrumbs'][] = 'Stats';
}
?>
<div class="monitor-stat-index">
    <div class="row">
        <div class="col-lg-3 col-lg-push-9">
            <?php FilterBar::begin() ?>
            <?= $this->render('/job/_job-filter', compact('filter')) ?>
            <?php FilterBar::end() ?>
        </div>
        <div class="col-lg-9 col-lg-pull-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $this->render('_chart-classes', ['filter' => $filter]) ?>
                </div>
                <div class="col-md-6">
                    <?= $this->render('_chart-senders', ['filter' => $filter]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
