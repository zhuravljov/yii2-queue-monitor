<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use zhuravljov\yii\queue\monitor\records\ExecRecord;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Attempts';
?>
<div class="monitor-job-attempts">
    <?= GridView::widget([
        'layout' => "{items}\n{pager}",
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
            [
                'header' => 'Duration',
                'format' => 'duration',
                'value' => function ($record) {
                     /**@var ExecRecord $record**/
                    return $record->presenter()->executionTime();
                },
            ],
            'retry:boolean',
        ],
        'rowOptions' => function ($record) {
            /**@var ExecRecord $record**/
            $options = [];
            if ($record->presenter()->hasExecutionError()) {
                Html::addCssClass($options, 'danger');
            }
            return $options;
        },
        'afterRow' => function ($record, $key, $index, GridView $grid) {
            /**@var ExecRecord $record**/
            if ($record->presenter()->hasExecutionError()) {
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