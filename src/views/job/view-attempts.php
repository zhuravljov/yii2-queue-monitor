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

$this->params['breadcrumbs'][] = 'Attempts';

$format = Module::getInstance()->formatter;
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
        'formatter' => $format,
        'columns' => [
            'attempt:integer',
            'reserved_at:datetime:Started',
            'done_at:time:Finished',
            'duration:duration',
            'retry:boolean',
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
                return strtr('<tr class="error-line danger text-danger"><td colspan="5">{error}</td></tr>', [
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
