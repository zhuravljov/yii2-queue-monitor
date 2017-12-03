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
<canvas id="chart-classes"></canvas>
<?php
StatIndexAsset::register($this);
$data = Json::encode($filter->searchClasses());
$filterUrl = Url::to(['job/index', 'class' => '_value_'] + JobFilter::restoreParams());
$this->registerJs(<<<JS
renderPie('chart-classes', $data, function(d) {
    location.href = '$filterUrl'.replace('_value_', encodeURI(d.name));
});
JS
);
