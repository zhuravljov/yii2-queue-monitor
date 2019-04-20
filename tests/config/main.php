<?php
return [
    'id' => 'yii2-queue-monitor-app',
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
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => sprintf(
                'mysql:host=%s;dbname=%s',
                getenv('MYSQL_HOST'),
                getenv('MYSQL_DATABASE')
            ),
            'username' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'attributes' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode = "STRICT_ALL_TABLES"',
            ],
        ],
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
