<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\helpers\Json;
use yii\helpers\Url;
use zhuravljov\yii\queue\monitor\assets\StatIndexAsset;
use zhuravljov\yii\queue\monitor\filters\JobFilter;

?>
<canvas id="chart-senders"></canvas>
<?php
StatIndexAsset::register($this);
$data = Json::encode($filter->searchSenders());
$filterUrl = Url::to(['job/index', 'sender' => '_value_'] + JobFilter::restoreParams());
$this->registerJs(<<<JS
renderPie('chart-senders', $data, function(d) {
    location.href = '$filterUrl'.replace('_value_', encodeURI(d.name));
});
JS
);
