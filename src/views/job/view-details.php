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
use zhuravljov\yii\queue\monitor\widgets\LinkPager;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Details';

JobItemAsset::register($this);
?>
<div class="monitor-job-details">
    <?= DetailView::widget([
        'model' => $record,
        'formatter' => Module::getInstance()->formatter,
        'attributes' => [
            'sender_name:text:Sender',
            'job_uid:text:Job UID',
            'job_class:text:Class',
            'push_ttr:integer:Push TTR',
            'push_delay:integer:Delay',
            'pushed_at:relativeTime:Pushed',
            'waitTime:duration',
            'status:text',
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
        'layout' => "<h3>Sub Jobs</h3>\n{items}\n{pager}",
        'pager' => [
            'class' => LinkPager::class,
        ],
        'itemView' => '_index-item',
        'itemOptions' => ['tag' => null],
        'emptyText' => false,
    ]) ?>
    <?php Pjax::end() ?>
</div>
