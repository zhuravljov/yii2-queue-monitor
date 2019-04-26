<?php
/**
 * @var \yii\web\View $this
 */

use yii\bootstrap\Alert;
use yii\helpers\Html;

$aliases = [
    'info' => 'alert-info',
    'success' => 'alert-success',
    'warning' => 'alert-warning',
    'error' => 'alert-danger',
];
?>
<div>
    <?php foreach (Yii::$app->session->getAllFlashes(true) as $type => $message): ?>
        <div>
            <?= Alert::widget([
                'options' => [
                    'class' => isset($aliases[$type]) ? $aliases[$type] : $type,
                ],
                'body' => Html::encode($message),
            ]) ?>
        </div>
    <?php endforeach ?>
</div>
