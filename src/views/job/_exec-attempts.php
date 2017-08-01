<?php
/**
 * @var \yii\web\View $this
 * @var \zhuravljov\yii\queue\monitor\records\ExecRecord[] $execs
 */

use yii\bootstrap\Html;

if (!count($execs)) return;

$format = Yii::$app->formatter;
?>
<div class="monitor-job-exec-attempts">
    <table class="table">
        <thead>
            <tr>
                <th>Attempt</th>
                <th>Started</th>
                <th>Done</th>
                <th>Retry</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($execs as $exec): ?>
            <tr class="<?= $exec->error !== null ? 'warning text-warning' : '' ?>">
                <th>#<?= $format->asInteger($exec->attempt) ?></th>
                <td><?= $format->asDatetime($exec->reserved_at) ?></td>
                <td><?= $format->asRelativeTime($exec->done_at, $exec->reserved_at) ?></td>
                <td><?= $format->asBoolean($exec->retry) ?></td>
            </tr>
            <?php if ($exec->error !== null): ?>
                <tr class="warning text-warning">
                    <td colspan="4">
                        <?= nl2br(Html::encode($exec->error)) ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

