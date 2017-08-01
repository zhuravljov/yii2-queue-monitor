<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $record
 */

$this->params['breadcrumbs'][] = ['label' => 'Jobs', 'url' => ['index']];
$this->params['breadcrumbs'][]  = '#' . $record->id;
?>
<div class="monitor-job-view">
    <p>
        <a href="<?= \yii\helpers\Url::to(['push', 'id' => $record->id]) ?>"
           class="btn btn-primary"
           data-method="post" data-confirm="Are you sure?"
        >
            <span class="glyphicon glyphicon-repeat"></span>
            Push again
        </a>
    </p>

    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#push-details" data-toggle="tab">
                Push details
            </a>
        </li>
        <li>
            <a href="#job-data" data-toggle="tab">
                Job data
            </a>
        </li>
        <li>
            <a href="#exec-attempts" data-toggle="tab">
                Attempts (<?= count($record->execs) ?>)
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="push-details" class="tab-pane active">
            <?= $this->render('_push-details', [
                'record' => $record,
            ]) ?>
        </div>
        <div id="job-data" class="tab-pane">
            <?= $this->render('_job-data', [
                'job' => unserialize($record->job_object),
            ]) ?>
        </div>
        <div id="exec-attempts" class="tab-pane">
            <?= $this->render('_exec-attempts', [
                'execs' => $record->execs,
            ]) ?>
        </div>
    </div>
</div>
