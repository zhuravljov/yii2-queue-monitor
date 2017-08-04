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

foreach (Yii::$app->session->getAllFlashes(true) as $type => $message) {
    $class = isset($aliases[$type]) ? $aliases[$type] : $type;
    echo Alert::widget([
        'options' => ['class' => $class],
        'body' => Html::encode($message),
    ]);
}