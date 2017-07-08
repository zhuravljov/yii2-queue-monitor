<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\filters\JobFilter $filter
 */

use yii\grid\GridView;

$this->params['breadcrumbs'][]  = 'Jobs';
?>
<div class="monitor-job-index">
    <?= GridView::widget([
        'dataProvider' => $filter->search(),
        'filterModel' => $filter,
        'columns' => [
            'sender',
            'job_uid',
            'job_class',
            'delay',
            'pushed_at:relativeTime',
            'status',
            [
                'class' => \yii\grid\ActionColumn::class,
                'template' => '{view}'
            ],
        ],
    ]) ?>
</div>
