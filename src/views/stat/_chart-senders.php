<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\helpers\Url;
use zhuravljov\yii\queue\monitor\assets\StatIndexAsset;
use zhuravljov\yii\queue\monitor\filters\JobFilter;

?>
<canvas id="chart-senders"></canvas>
<?php
StatIndexAsset::register($this);
$params = JobFilter::restoreParams();
$dataUrl = Url::to(['stat/sender-list'] + $params);
$filterUrl = Url::to(['job/index', 'sender' => '_value_'] + $params);
$this->registerJs(<<<JS
$.getJSON('$dataUrl').done(function(data) {
    renderPie('chart-senders', data, function(d) {
        location.href = '$filterUrl'.replace('_value_', encodeURI(d.name));
    });
});
JS
);
