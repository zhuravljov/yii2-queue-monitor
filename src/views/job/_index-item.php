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
$status = $model->getStatus();
switch ($status) {
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
    <div class="job-status"><?= $format->asText($model->getStatusLabel($status)) ?></div>
    <div class="job-details">
        <div class="job-push-uid">
            <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" data-pjax="0">
                <?= Module::t('main', '#{jobId} by {sender}', [
                    'jobId' => $format->asText($model->job_uid),
                    'sender' => $format->asText($model->sender_name)
                ]) ?>
            </a>
            <?php if ($model->parent): ?>
                <?= Module::t('main', 'from') ?>
                <a href="<?= Url::to(['view', 'id' => $model->parent->id]) ?>" data-pjax="0">
                    #<?= $format->asText($model->parent->job_uid) ?>
                </a>
            <?php endif ?>
        </div>
        <div class="job-push-time">
            <?= Module::t('main', 'Pushed') ?>: <?= $format->asDatetime($model->pushed_at) ?>
        </div>
        <div class="job-push-ttr" title="<?= Module::t('main', 'Time to reserve of the job.') ?>">
            <?= Module::t('main', 'TTR') ?>: <?= $format->asInteger($model->ttr) ?>s
        </div>
        <div class="job-push-delay">
            <?= Module::t('main', 'Delay') ?>: <?= $format->asInteger($model->delay) ?>s
        </div>
        <div class="job-exec-attempts" title="<?= Module::t('main', 'Number of attempts.') ?>">
            <?= Module::t('main', 'Attempts') ?>: <?= $format->asInteger($model->getAttemptCount()) ?>
        </div>
        <div class="job-exec-wait-time" title="<?= Module::t('main', 'Waiting time from push till first execute.') ?>">
            <?= Module::t('main', 'Wait') ?>: <?= $format->asInteger($model->getWaitTime()) ?>s
        </div>
        <?php if ($model->lastExec): ?>
            <div class="job-exec-time" title="<?= Module::t('main', 'Last execute time and memory usage.') ?>">
                <?= Module::t('main', 'Exec') ?>: <?= $format->asInteger($model->lastExec->getDuration()) ?>s
                <?php if ($model->lastExec->memory_usage): ?>
                    / <?= $format->asShortSize($model->lastExec->memory_usage, 0) ?>
                <?php endif ?>
            </div>
        <?php endif ?>
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
            <strong><?= Module::t('main', 'Error') ?>:</strong>
            <?= $format->asText($model->lastExec->getErrorMessage()) ?>
        </div>
    <?php endif ?>
    <div class="job-border"></div>
</div>
