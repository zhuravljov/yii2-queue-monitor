<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor;

use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;

/**
 * Class Config
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @var bool
     */
    public $canPushAgain = true;
    /**
     * @var bool
     */
    public $canStop = true;
    /**
     * @inheritdoc
     */
    public $layout = 'main';
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'zhuravljov\yii\queue\monitor\controllers';
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'job/stats';

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof WebApplication) {
            $app->urlManager->addRules([[
                'class' => GroupUrlRule::class,
                'prefix' => $this->id,
                'rules' => [
                    'stats' => 'job/stats',
                    'jobs' => 'job/list',
                    'job/<id:\d+>/<action\w+>' => 'job/view-<action>',
                    '<controller:\w+>/<id:\d+>' => '<controller>/view',
                    '<controller:\w+>/<action\w+>/<id:\d+>' => '<controller>/<action>',
                    '<controller:\w+>/<action\w+>' => '<controller>/<action>',
                ],
            ]], false);
        } else {
            throw new InvalidConfigException('The module must be used for web application only.');
        }
    }
}
