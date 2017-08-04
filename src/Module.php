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

/**
 * Class Config
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    public $layout = 'main';
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'zhuravljov\yii\queue\monitor\controllers';
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'job';

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof WebApplication) {
            $app->urlManager->addRules([
                $this->id . '/jobs' => $this->id . '/job/index',
                $this->id . '/job/<id:\d+>/<action\w+>' => $this->id . '/job/view-<action>',
                $this->id . '/<controller:\w+>/<id:\d+>' => $this->id . '/<controller>/view',
                $this->id . '/<controller:\w+>/<action\w+>/<id:\d+>' => $this->id . '/<controller>/<action>',
                $this->id . '/<controller:\w+>/<action\w+>' => $this->id . '/<controller>/<action>',
            ], false);
        } else {
            throw new InvalidConfigException('The module must be used for web application only.');
        }
    }
}
