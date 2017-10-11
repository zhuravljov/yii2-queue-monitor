<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use zhuravljov\yii\queue\monitor\assets\JobStatsAsset;
use zhuravljov\yii\queue\monitor\filters\JobFilter;

if (JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = ['label' => 'Stats', 'url' => ['stats']];
    $this->params['breadcrumbs'][] = 'Filtered';
} else {
    $this->params['breadcrumbs'][] = 'Stats';
}
?>
<?php $this->beginContent(__DIR__ . '/_index-layout.php', ['filter' => $filter]) ?>
<div class="monitor-job-stats">
</div>
<?php $this->endContent() ?>
<?php
JobStatsAsset::register($this);
$this->registerJs(<<<JS

JS
);
