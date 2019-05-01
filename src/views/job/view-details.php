<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\data\ActiveDataProvider;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use zhuravljov\yii\queue\monitor\assets\JobItemAsset;
use zhuravljov\yii\queue\monitor\Module;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = Module::t('main', 'Details');

JobItemAsset::register($this);
?>
<div class="monitor-job-details">
    <?= DetailView::widget([
        'model' => $record,
        'formatter' => Module::getInstance()->formatter,
        'attributes' => [
            [
                'attribute' => 'sender_name',
                'format' => 'text',
                'label' => Module::t('main', 'Sender'),
            ],
            [
                'attribute' => 'job_uid',
                'format' => 'text',
                'label' => Module::t('main', 'Job UID'),
            ],
            [
                'attribute' => 'job_class',
                'format' => 'text',
                'label' => Module::t('main', 'Class'),
            ],
            [
                'attribute' => 'ttr',
                'format' => 'integer',
                'label' => Module::t('main', 'Push TTR'),
            ],
            [
                'attribute' => 'delay',
                'format' => 'integer',
                'label' => Module::t('main', 'Delay'),
            ],
            [
                'attribute' => 'pushed_at',
                'format' => 'relativeTime',
                'label' => Module::t('main', 'Pushed'),
            ],
            [
                'attribute' => 'waitTime',
                'format' => 'duration',
                'label' => Module::t('main', 'Wait Time'),
            ],
            [
                'attribute' => 'status',
                'format' => 'text',
                'value' => function ($model) {
                    return $model->getStatusLabel($model->getStatus());
                },
                'label' => Module::t('main', 'Status'),
            ],
        ],
        'options' => ['class' => 'table table-hover'],
    ]) ?>
    
    <?php Pjax::begin() ?>
    <?= ListView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $record->getChildren()
                              ->with(['parent', 'firstExec', 'lastExec', 'execTotal']),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]),
        'layout' => '<h3>' . Module::t('main', 'Sub Jobs') . "</h3>\n{items}\n{pager}",
        'itemView' => '_index-item',
        'itemOptions' => ['tag' => null],
        'emptyText' => false,
    ]) ?>
    <?php Pjax::end() ?>
</div>
