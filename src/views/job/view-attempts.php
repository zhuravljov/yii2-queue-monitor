<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use zhuravljov\yii\queue\monitor\Module;
use zhuravljov\yii\queue\monitor\records\ExecRecord;
use zhuravljov\yii\queue\monitor\widgets\LinkPager;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = Module::t('main', 'Attempts');

$format = Module::getInstance()->formatter;
?>
<div class="monitor-job-attempts">
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $record->getExecs(),
            'sort' => [
                'attributes' => [
                    'attempt',
                ],
                'defaultOrder' => [
                    'attempt' => SORT_DESC,
                ],
            ],
        ]),
        'layout' => "{items}\n{pager}",
        'pager' => [
            'class' => LinkPager::class,
        ],
        'emptyText' => Module::t('main', 'No workers found.'),
        'tableOptions' => ['class' => 'table table-hover'],
        'formatter' => $format,
        'columns' => [
            [
                'attribute' => 'attempt',
                'format' => 'integer',
                'label' => Module::t('main', 'Attempt')
            ],
            [
                'attribute' => 'started_at',
                'format' => 'datetime',
                'label' => Module::t('main', 'Started')
            ],
            [
                'attribute' => 'finished_at',
                'format' => 'time',
                'label' => Module::t('main', 'Finished')
            ],
            [
                'attribute' => 'duration',
                'format' => 'duration',
                'label' => Module::t('main', 'Duration')
            ],
            [
                'attribute' => 'memory_usage',
                'format' => 'shortSize',
                'label' => Module::t('main', 'Memory Usage')
            ],
            [
                'attribute' => 'retry',
                'format' => 'boolean',
                'label' => Module::t('main', 'Is retry?')
            ],
        ],
        'rowOptions' => function (ExecRecord $record) {
            $options = [];
            if ($record->isFailed()) {
                Html::addCssClass($options, 'danger');
            }
            return $options;
        },
        'afterRow' => function (ExecRecord $record) use ($format) {
            if ($record->isFailed()) {
                return strtr('<tr class="error-line danger text-danger"><td colspan="6">{error}</td></tr>', [
                    '{error}' => $format->asNtext($record->error),
                ]);
            }
            return '';
        },
    ]) ?>
</div>
<?php
$this->registerCss(
<<<CSS
tr.error-line > td {
    white-space: normal;
    word-break: break-all;
}
CSS
);
