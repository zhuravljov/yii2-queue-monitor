<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $model
 */

use yii\helpers\Url;
use yii\helpers\VarDumper;
use zhuravljov\yii\queue\monitor\Module;

$format = Module::getInstance()->formatter;
?>
<div class="job-status"><?= $format->asText($model->getStatus()) ?></div>
<div class="job-details">
    <div class="job-push-uid">
        <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" data-pjax="0">
            #<?= $format->asText($model->job_uid) ?> by <?= $format->asText($model->sender_name) ?>
        </a>
    </div>
    <div class="job-push-time">
        Pushed: <?= $format->asDatetime($model->pushed_at) ?>
    </div>
    <div class="job-push-ttr" title="Time to reserve of the job.">
        TTR: <?= $format->asInteger($model->ttr) ?>s
    </div>
    <div class="job-push-delay">
        Delay: <?= $format->asInteger($model->delay) ?>s
    </div>
    <div class="job-exec-attempts" title="Number of attempts.">
        Attempts: <?= $format->asInteger($model->getAttemptCount()) ?>
    </div>
    <div class="job-exec-wait-time" title="Waiting time from push till first execute.">
        Wait: <?= $format->asInteger($model->getWaitTime()) ?>s
    </div>
    <?php if ($model->lastExec): ?>
        <div class="job-exec-time" title="Last execute time.">
            Exec: <?= $format->asInteger($model->lastExec->getDuration()) ?>s
        </div>
    <?php endif; ?>
</div>
<div class="job-class">
    <?= $format->asText($model->job_class) ?>
</div>
<div class="job-params">
    <?php foreach ($model->getJobParams() as $property => $value): ?>
        <span class="job-param">
            <span class="job-param-name"><?= $format->asText($property) ?> =</span>
            <span class="job-param-value"><?= VarDumper::dumpAsString($value) ?></span>
        </span>
    <?php endforeach ?>
</div>
<?php if ($model->lastExec && $model->lastExec->isFailed()): ?>
    <div class="job-error text-danger">
        <strong>Error:</strong>
        <?= $format->asText($model->lastExec->getErrorMessage()) ?>
    </div>
<?php endif; ?>
<div class="job-border"></div>
