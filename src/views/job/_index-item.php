<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $model
 */

use yii\helpers\VarDumper;

$f = Yii::$app->formatter;
?>
<div class="job-status"><?= $f->asText($model->getStatus()) ?></div>
<div class="job-details">
    <div class="job-push-uid">
        <a href="<?= \yii\helpers\Url::to(['view', 'id' => $model->id]) ?>">
            #<?= $f->asText($model->job_uid) ?> by <?= $f->asText($model->sender_name) ?>
        </a>
    </div>
    <div class="job-push-time">
        Pushed: <?= $f->asDatetime($model->pushed_at, 'php:Y-m-d H:i:s') ?>
    </div>
    <div class="job-push-ttr" title="Time to reserve of the job.">
        TTR: <?= $f->asInteger($model->ttr) ?>s
    </div>
    <div class="job-push-delay">
        Delay: <?= $f->asInteger($model->delay) ?>s
    </div>
    <div class="job-exec-attempts" title="Number of attempts.">
        Attempts: <?= $f->asInteger($model->getAttemptCount()) ?>
    </div>
    <div class="job-exec-wait-time" title="Waiting time from push till first execute.">
        Wait: <?= $f->asInteger($model->getWaitTime()) ?>s
    </div>
    <?php if ($model->lastExec): ?>
        <div class="job-exec-time" title="Last execute time.">
            Exec: <?= $f->asInteger($model->lastExec->getDuration()) ?>s
        </div>
    <?php endif; ?>
</div>
<div class="job-class">
    <?= $f->asText($model->job_class) ?>
</div>
<div class="job-params">
    <?php foreach ($model->getJobParams() as $property => $value): ?>
        <span class="job-param">
            <span class="job-param-name"><?= $f->asText($property) ?> =</span>
            <span class="job-param-value"><?= VarDumper::dumpAsString($value) ?></span>
        </span>
    <?php endforeach ?>
</div>
<?php if ($model->lastExec && $model->lastExec->error !== null): ?>
    <div class="job-error text-danger">
        <strong>Error:</strong>
        <?= $f->asText($model->lastExec->getErrorMessage()) ?>
    </div>
<?php endif; ?>
<div class="job-border"></div>