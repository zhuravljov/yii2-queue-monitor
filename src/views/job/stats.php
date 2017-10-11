<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 * @var array $classes
 * @var array $senders
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
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <canvas id="chart-classes"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <canvas id="chart-senders"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endContent() ?>
<?php
JobStatsAsset::register($this);
$classesData = \yii\helpers\Json::encode($classes);
$sendersData = \yii\helpers\Json::encode($senders);
$this->registerJs(<<<JS
renderClassesPie('chart-classes', $classesData);
renderSendersPie('chart-senders', $sendersData);
JS
);
