<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use zhuravljov\yii\queue\monitor\filters\JobFilter;

if ($filterParams = JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = ['label' => 'Stats', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Filtered';
} else {
    $this->params['breadcrumbs'][] = 'Stats';
}
?>
<?php $this->beginContent(dirname(__DIR__) . '/layouts/job-filter.php', ['filter' => $filter]) ?>
<div class="monitor-stat-index">
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_chart-classes', ['filter' => $filter]) ?>
        </div>
        <div class="col-md-6">
            <?= $this->render('_chart-senders', ['filter' => $filter]) ?>
        </div>
    </div>
</div>
<?php $this->endContent() ?>
