<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\bootstrap\Html;
use yii\widgets\ListView;
use zhuravljov\yii\queue\monitor\filters\JobFilter;
use zhuravljov\yii\queue\monitor\records\PushRecord;

if (JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = ['label' => 'Jobs', 'url' => ['list']];
    $this->params['breadcrumbs'][] = 'Filtered';
} else {
    $this->params['breadcrumbs'][] = 'Jobs';
}
?>
<?php $this->beginContent(__DIR__ . '/_index-layout.php', ['filter' => $filter]) ?>
<div class="monitor-job-list">
    <?= ListView::widget([
        'itemView' => '_index-item',
        'itemOptions' => function (PushRecord $push) {
            $options = ['class' => 'job-item'];
            switch ($push->getStatus()) {
                case PushRecord::STATUS_STOPPED:
                    Html::addCssClass($options, 'bg-info');
                    break;
                case PushRecord::STATUS_WAITING:
                case PushRecord::STATUS_STARTED:
                    Html::addCssClass($options, 'bg-success');
                    break;
                case PushRecord::STATUS_FAILED:
                case PushRecord::STATUS_RESTARTED:
                    Html::addCssClass($options, 'bg-warning');
                    break;
                case PushRecord::STATUS_BURIED:
                    Html::addCssClass($options, 'bg-danger');
                    break;
                default:
                    Html::addCssClass($options, 'bg-default');
            }
            return $options;
        },
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $filter->search()->with(['firstExec', 'lastExec', 'execCount']),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]),
    ]) ?>
</div>
<?php $this->endContent() ?>
<?php
$this->registerCss(<<<CSS

.job-item {
    position: relative;
    padding: 10px;
}
.job-status {
    float:right;
    font-weight: bold;
    text-transform: uppercase;
}
.job-status,
.job-details {
    font-size: 90%;
}
.job-details > * {
    display: inline-block;
    margin-right: 25px;
}
.job-push-uid {
    font-weight: bold;
}
.job-class {
    margin-top: 5px;
    font-size: 125%;
    font-weight: bold;
}
.job-params {
    font-size: 85%;
    color: #777;
}
.job-param {
    margin-right: 5px;
}
.job-param:last-child {
    margin-right: 0;
}
.job-param-name,
.job-param-value:after {
    font-weight: bold;
}
.job-param-name {
    white-space: nowrap;
}
.job-param-value {
    word-break: break-all;
}
.job-param-value:after {
    content: ";"
}
.job-param:last-child > .job-param-value:after {
    content: ""
}
.job-error {
    margin-top: 10px;
    font-size: 90%;
    word-break: break-all;
}

.job-item.bg-default {
    border-top: 1px solid #ddd;
}
.job-item.bg-default:hover {
    background-color: #eee;
}
.job-item:hover,
.job-item.bg-info,
.job-item.bg-success,
.job-item.bg-warning,
.job-item.bg-danger {
    border-radius: 4px;
    border-top: 1px solid #fff;
}
:hover + .job-item.bg-default,
.bg-info + .job-item.bg-default,
.bg-success + .job-item.bg-default,
.bg-warning + .job-item.bg-default,
.bg-danger + .job-item.bg-default {
    border-top-color: #fff;
}
.job-item {
    padding-left: 17px;
}
.job-border {
    position: absolute;
    display: none;
    left: 0;
    top: 0;
    bottom: 0;
    width: 7px;
    border-radius: 4px 0 0 4px;
    border-right: 1px solid #fff;
    background-color: #999;
}
.job-item:hover .job-border {
    display: block;
}

CSS
);