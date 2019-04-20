<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\i18n\PhpMessageSource;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;

/**
 * Web Module
 *
 * @property \yii\i18n\Formatter $formatter
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @var bool
     */
    public $canPushAgain = false;
    /**
     * @var bool
     */
    public $canExecStop = false;
    /**
     * @var bool
     */
    public $canWorkerStop = false;
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
    public $defaultRoute = 'job/index';
    
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }
    
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
                    'jobs' => 'job/index',
                    'job/<id:\d+>/<action\w+>' => 'job/view-<action>',
                    'workers' => 'worker/index',
                    '<controller:\w+>/<id:\d+>' => '<controller>/view',
                    '<controller:\w+>/<action\w+>/<id:\d+>' => '<controller>/<action>',
                    '<controller:\w+>/<action\w+>' => '<controller>/<action>',
                ],
            ]], false);
        } else {
            throw new InvalidConfigException('The module must be used for web application only.');
        }
    }
    
    private function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations['queue-monitor/*'])) {
            Yii::$app->i18n->translations['queue-monitor/*'] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@zhuravljov/yii/queue/monitor/messages',
                'fileMap' => [
                    'queue-monitor/main' => 'main.php',
                    'queue-monitor/notice' => 'notice.php',
                ],
            ];
        }
    }
    
    /**
     * Module translator.
     *
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('queue-monitor/' . $category, $message, $params, $language);
    }
}
