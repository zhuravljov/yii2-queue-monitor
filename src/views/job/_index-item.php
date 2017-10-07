<?php
/**
 * @var \yii\web\View                                    $this
 * @var \zhuravljov\yii\queue\monitor\records\PushRecord $model
 */

use yii\helpers\VarDumper;

$f = Yii::$app->formatter;
?>
<div class="job-status"><?= $model->status()->value() ?></div>
<div class="job-details">
    <div class="job-push-uid">
        <a href="<?= \yii\helpers\Url::to(['view', 'id' => $model->id]) ?>">
            #<?= $model->presenter()->jobUid() ?> by <?= $model->presenter()->senderName() ?>
        </a>
    </div>
    <div class="job-push-time">
        Pushed: <?= $model->presenter()->pushedAt() ?>
    </div>
    <div class="job-push-ttr" title="Time to reserve of the job.">
        TTR: <?= $model->presenter()->ttr() ?>s
    </div>
    <div class="job-push-delay">
        Delay: <?= $model->presenter()->delay() ?>s
    </div>
    <div class="job-exec-attempts" title="Number of attempts.">
        Attempts: <?= $model->presenter()->attempts() ?>
    </div>
    <?php if ($model->firstExec) : ?>
        <div class="job-exec-wait-time" title="Waiting time from push till first execute.">
            Wait: <?= $model->presenter()->waitTimeTillExecute() ?>s
        </div>
    <?php endif; ?>
    <?php if ($model->lastExec && $model->lastExec->presenter()->isDone()) : ?>
        <div class="job-exec-time" title="Last execute time.">
            Exec: <?= $model->presenter()->lastExecutionTime() ?>s
        </div>
    <?php endif; ?>
</div>
<div class="job-class">
    <?= $f->asText($model->job_class) ?>
</div>
<div class="job-params">
    <?php foreach ($model->presenter()->jobAttributes() as $property => $value) : ?>
        <span class="job-param">
            <span class="job-param-name"><?= $f->asText($property) ?> =</span>
            <span class="job-param-value"><?= VarDumper::dumpAsString($value) ?></span>
        </span>
    <?php endforeach ?>
</div>
<?php if ($model->lastExec && $model->lastExec->presenter()->hasExecutionError()) : ?>
    <div class="job-error text-danger">
        <strong>Error:</strong>
        <?= $f->asText($model->lastExec->getErrorLine()) ?>
    </div>
<?php endif; ?>
<div class="job-border"></div>