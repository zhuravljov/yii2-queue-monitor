<?php
/**
 * @var \yii\web\View $this
 * @var WorkerFilter $filter
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use zhuravljov\yii\queue\monitor\filters\WorkerFilter;
use zhuravljov\yii\queue\monitor\Module;
use zhuravljov\yii\queue\monitor\records\WorkerRecord;

$this->params['breadcrumbs'][] = 'Workers';

$format = Module::getInstance()->formatter;
?>
<div class="worker-index">
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $filter->search()->active()->with(['execTotal', 'lastExec']),
            'sort' => [
                'attributes' => [
                    'started_at' => [
                        'asc' => ['sender_name' => SORT_ASC, 'id' => SORT_ASC],
                        'desc' => ['sender_name' => SORT_ASC, 'id' => SORT_DESC],
                    ],
                ],
                'defaultOrder' => [
                    'started_at' => SORT_ASC,
                ],
            ],
        ]),
        'layout' => "{items}\n{pager}",
        'emptyText' => 'No workers found.',
        'tableOptions' => ['class' => 'table table-hover'],
        'formatter' => $format,
        'columns' => [
            'started_at:datetime',
            'pid',
            'execTotalDone:integer',
            'lastExec.reserved_at:datetime:Last Job',
            [
                'class' => ActionColumn::class,
                'template' => '{stop}',
                'buttons' => [
                    'stop' => function ($url) {
                        return Html::a(Html::icon('stop'), $url, [
                            'data' => ['method' => 'post', 'confirm' => 'Are you sure?'],
                            'title' => 'Stop the worker.',
                        ]);
                    }
                ],
                'visibleButtons' => [
                    'stop' => function (WorkerRecord $model) {
                        return Module::getInstance()->canWorkerStop && !$model->isStopped();
                    }
                ],
            ],
        ],
        'beforeRow' => function (WorkerRecord $record) use ($format) {
            static $senderName;
            if ($senderName === $record->sender_name) {
                return '';
            }
            $groupTitle = strtr('Sender: name (class)', [
                'name' => $record->sender_name,
                'class' => get_class(Yii::$app->get($record->sender_name)),
            ]);
            return Html::tag('tr', Html::tag('th', $format->asText($groupTitle), ['colspan' => 6]));
        },
    ]) ?>
</div>
