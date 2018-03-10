<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

use yii\bootstrap\Html;
use zhuravljov\yii\queue\monitor\Module;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Environment';

$format = Module::getInstance()->formatter;
?>
<div class="monitor-job-env">
    <h3>Push Trace</h3>
    <?= Html::ol($record->pushTrace) ?>
    <h3>$_SERVER</h3>
    <?= $this->render('_table', ['values' => $record->pushEnv]) ?>
</div>
