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
            'phpDocBlock' => <<<DOC
                /**
                 * @link https://github.com/zhuravljov/yii2-queue-monitor
                 * @copyright Copyright (c) 2017 Roman Zhuravlev
                 * @license http://opensource.org/licenses/BSD-3-Clause
                 */
                
                DOC,
        ],
        'qmonitor' => [
            'class' => \zhuravljov\yii\queue\monitor\console\GcController::class,
            'silent' => false,
        ],
    ],
];
