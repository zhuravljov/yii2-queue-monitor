<?php
require(__DIR__ . '/../vendor/autoload.php');

define('YII_ENV', 'dev');
define('YII_DEBUG', true);

require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@zhuravljov/yii/queue/monitor', dirname(__DIR__) . '/src');
Yii::setAlias('@tests', __DIR__);
