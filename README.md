Yii2 Queue Analytics Module
===========================

The module collects statistics about working of queues of an application, and provides web interface
for research. Also the module allows to stop and replay any jobs manually.

[![Latest Stable Version](https://poser.pugx.org/zhuravljov/yii2-queue-monitor/v/stable.svg)](https://packagist.org/packages/zhuravljov/yii2-queue-monitor)
[![Total Downloads](https://poser.pugx.org/zhuravljov/yii2-queue-monitor/downloads.svg)](https://packagist.org/packages/zhuravljov/yii2-queue-monitor)

Installation
------------

The preferred way to install the extension is through [composer](http://getcomposer.org/download/).
Add to the require section of your `composer.json` file:

```
"zhuravljov/yii2-queue-monitor": "1.0.x-dev"
```

Usage
-----

To configure the statistics collector, you need to add monitor behavior for each queue component. 
Update common config file:

```php
return [
    'components' => [
        'queue' => [
            // ...
            'as monitor' => \zhuravljov\yii\queue\monitor\Behavior::class,
        ],
    ],
];
```

There are storage options that you can configure by common config file:

```php
return [
    'container' => [
        'singletons' => [
            \zhuravljov\yii\queue\monitor\Env::class => [
                'cache' => 'cache',
                'db' => 'db',
                'pushTableName' => '{{%queue_push}}',
                'execTableName' => '{{%queue_exec}}',
            ],
        ],
    ],
];
```

If you want use migrations of the extension, configure migration command in console config:

```php
'controllerMap' => [
    'migrate' => [
        'class' => \yii\console\controllers\MigrateController::class,
        'migrationNamespaces' => [
            //...
            'zhuravljov\yii\queue\monitor\migrations',
        ],
    ],
],
```

And apply migrations.

Finally, modify your web config file to turn on web interface:

```php
return [
    'bootstrap' => [
        'monitor',
    ],
    'modules' => [
        'monitor' => [
            'class' => \zhuravljov\yii\queue\monitor\Module::class,
        ],
    ],
];
```

It will be available by URL `http://yourhost.com/monitor`.