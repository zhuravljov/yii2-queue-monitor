<?php
require(__DIR__ . '/../bootstrap.php');
$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/web.php')
);
(new yii\web\Application($config))->run();
