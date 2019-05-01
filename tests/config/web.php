<?php /** @noinspection PhpFullyQualifiedNameUsageInspection */
return [
    'controllerNamespace' => 'tests\app\controllers',
    'viewPath' => '@tests/app/views',
    'defaultRoute' => 'test',
    'bootstrap' => [
        'debug',
        'qmonitor',
    ],
    'components' => [
        'request' => [
            'class' => \yii\web\Request::class,
            'cookieValidationKey' => '1234567890',
        ],
        'urlManager' => [
            'class' => \yii\web\UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'modules' => [
        'debug' => [
            'class' => \yii\debug\Module::class,
            'allowedIPs' => ['*'],
            'panels' => [
                'queue' => \yii\queue\debug\Panel::class,
            ],
        ],
        'qmonitor' => [
            'class' => \zhuravljov\yii\queue\monitor\Module::class,
            'canPushAgain' => true,
            'canExecStop' => true,
            'canWorkerStop' => true,
            'layout' => '/test',
        ],
    ],
    'container' => [
        'definitions' => [
            \yii\data\Pagination::class => [
                'defaultPageSize' => 10,
                'pageSizeLimit' => [1, 100],
            ],
            \yii\widgets\LinkPager::class => \zhuravljov\yii\pagination\LinkPager::class,
            \zhuravljov\yii\pagination\LinkPager::class => [
                'maxButtonCount' => 5,
            ],
            \zhuravljov\yii\pagination\LinkSizer::class => [
                'sizes' => [10, 20, 50, 100],
            ],
        ],
    ],
];