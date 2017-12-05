<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\helpers\Url;
use zhuravljov\yii\queue\monitor\assets\StatIndexAsset;
use zhuravljov\yii\queue\monitor\filters\JobFilter;

?>
<canvas id="chart-classes"></canvas>
<?php
StatIndexAsset::register($this);
$params = JobFilter::restoreParams();
$dataUrl = Url::to(['stat/class-list'] + $params);
$jobUrl = Url::to(['job/index', 'class' => '_value_'] + $params);
$this->registerJs(<<<JS
$.getJSON('$dataUrl').done(function(data) {
    renderPie('chart-classes', data, function(d) {
        location.href = '$jobUrl'.replace('_value_', encodeURI(d.name));
    });
});
JS
);
