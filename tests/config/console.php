<?php
return [
    'controllerNamespace' => 'tests\app\commands',
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationPath' => null,
            'migrationNamespaces' => [
                'zhuravljov\yii\queue\monitor\migrations',
            ],
        ],
        'message' => [
            'class' => \yii\console\controllers\MessageController::class,
            'sourcePath' => '@zhuravljov/yii/queue/monitor',
            'messagePath' => '@zhuravljov/yii/queue/monitor/messages',
            'languages' => ['ru'],
            'translator' => 'Module::t',
            'sort' => true,
            'phpDocBlock' => '',
        ],
    ],
];
