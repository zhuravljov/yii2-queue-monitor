<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\assets;

use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;

/**
 * Class JobFilterAsset
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class JobFilterAsset extends AssetBundle
{
    public $sourcePath = '@zhuravljov/yii/queue/monitor/web';
    public $js = [
        'job-filter.js',
    ];
    public $css = [
        'job-filter.css',
    ];
    public $depends = [
        BootstrapPluginAsset::class,
    ];
}
