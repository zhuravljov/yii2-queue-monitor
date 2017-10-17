<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use zhuravljov\yii\queue\monitor\records\ExecRecord;
use zhuravljov\yii\queue\monitor\widgets\LinkPager;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Attempts';
?>
<div class="monitor-job-attempts">
    <?= GridView::widget([
        'layout' => "{items}\n{pager}",
        'pager' => [
            'class' => LinkPager::class,
        ],
        'tableOptions' => ['class' => 'table'],
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
        'columns' => [
            'attempt:integer',
            'reserved_at:datetime:Started',
            'done_at:time:Finished',
            'duration:duration',
            'retry:boolean',
        ],
        'rowOptions' => function (ExecRecord $record) {
            $options = [];
            if ($record->error !== null) {
                Html::addCssClass($options, 'danger');
            }
            return $options;
        },
        'afterRow' => function (ExecRecord $record, $key, $index, GridView $grid) {
            if ($record->error !== null) {
                return strtr('<tr class="error-line danger text-danger"><td colspan="5">{error}</td></tr>', [
                    '{error}' => $grid->formatter->asNtext($record->error),
                ]);
            } else {
                return '';
            }
        },
    ]) ?>
</div>
<?php
$this->registerCss(<<<CSS
tr.error-line > td {
    white-space: normal;
    word-break: break-all;
}
CSS
);