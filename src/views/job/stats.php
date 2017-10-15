<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 * @var array $classes
 * @var array $senders
 */

use yii\helpers\Json;
use yii\helpers\Url;
use zhuravljov\yii\queue\monitor\assets\JobStatsAsset;
use zhuravljov\yii\queue\monitor\filters\JobFilter;

if ($filterParams = JobFilter::restoreParams()) {
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
$classesData = Json::encode($classes);
$classesUrl = Url::to(['list', 'class' => '_value_'] + $filterParams);
$sendersData = Json::encode($senders);
$sendersUrl = Url::to(['list', 'sender' => '_value_'] + $filterParams);
$this->registerJs(<<<JS
renderPie('chart-classes', $classesData, function(d) {
    location.href = '$classesUrl'.replace('_value_', encodeURI(d.name));
});
renderPie('chart-senders', $sendersData, function(d) {
    location.href = '$sendersUrl'.replace('_value_', encodeURI(d.name));
});
JS
);
