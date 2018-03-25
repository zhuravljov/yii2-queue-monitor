<?php
/**
 * @var \yii\web\View $this
 * @var PushRecord $model
 */

use yii\helpers\Url;
use yii\helpers\VarDumper;
use zhuravljov\yii\queue\monitor\Module;
use zhuravljov\yii\queue\monitor\records\PushRecord;

$format = Module::getInstance()->formatter;

switch ($model->getStatus()) {
    case PushRecord::STATUS_STOPPED:
        $statusClass = 'bg-info';
        break;
    case PushRecord::STATUS_WAITING:
    case PushRecord::STATUS_STARTED:
        $statusClass = 'bg-success';
        break;
    case PushRecord::STATUS_FAILED:
    case PushRecord::STATUS_RESTARTED:
        $statusClass = 'bg-warning';
        break;
    case PushRecord::STATUS_BURIED:
        $statusClass = 'bg-danger';
        break;
    default:
        $statusClass = 'bg-default';
}
?>
<div class="job-item <?= $statusClass ?>">
    <div class="job-status"><?= $format->asText($model->getStatus()) ?></div>
    <div class="job-details">
        <div class="job-push-uid">
            <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" data-pjax="0">
                #<?= $format->asText($model->job_uid) ?> by <?= $format->asText($model->sender_name) ?>
            </a>
            <?php if ($model->parent): ?>
                from
                <a href="<?= Url::to(['view', 'id' => $model->parent->id]) ?>" data-pjax="0">
                    #<?= $format->asText($model->parent->job_uid) ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="job-push-time">
            Pushed: <?= $format->asDatetime($model->pushed_at) ?>
        </div>
        <div class="job-push-ttr" title="Time to reserve of the job.">
            TTR: <?= $format->asInteger($model->push_ttr) ?>s
        </div>
        <div class="job-push-delay">
            Delay: <?= $format->asInteger($model->push_delay) ?>s
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
            <span class="job-param-value"><?= htmlspecialchars(VarDumper::dumpAsString($value), ENT_QUOTES|ENT_SUBSTITUTE, Yii::$app->charset, true) ?></span>
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
</div>
