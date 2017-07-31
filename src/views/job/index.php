<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\filters\JobFilter $filter
 */

use yii\grid\GridView;

$this->params['breadcrumbs'][]  = 'Jobs';
?>
<div class="monitor-job-index">
    <div class="row">
        <div class="col-lg-3 col-lg-push-9">
            <?= $this->render('_search', [
                'filter' => $filter,
            ]) ?>
        </div>
        <div class="col-lg-9 col-lg-pull-3">
            <?= GridView::widget([
                'dataProvider' => $filter->search(),
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
    </div>
</div>
