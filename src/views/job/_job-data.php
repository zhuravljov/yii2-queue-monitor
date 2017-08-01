<?php
/**
 * @var \yii\web\View $this
 * @var \yii\queue\Job $job
 */

use yii\bootstrap\Html;
use yii\helpers\VarDumper;
?>
<div class="monitor-job-data">
    <table class="table">
        <tbody>
            <tr>
                <th>class</th>
                <td><?= Html::encode(get_class($job)) ?></td>
            </tr>
            <?php foreach (get_object_vars($job) as $property => $value): ?>
                <tr>
                    <th><?= Html::encode($property) ?></th>
                    <td><?= VarDumper::dumpAsString($value) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>