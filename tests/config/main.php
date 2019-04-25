<?php
return [
    'id' => 'yii2-queue-monitor-app',
    'timeZone' => getenv('TZ'),
    'basePath' => dirname(__DIR__) . '/app',
    'runtimePath' => dirname(__DIR__) . '/runtime',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'bootstrap' => [
        'queue',
    ],
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'db' => require(__DIR__ . '/db.php'),
        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'as job-monitor' => \zhuravljov\yii\queue\monitor\JobMonitor::class,
            'as worker-monitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class,
        ],
    ],
    'container' => [
        'singletons' => [
            \zhuravljov\yii\queue\monitor\Env::class => [
                'cache' => 'cache',
                'db' => 'db',
            ],
        ],
    ],
];
