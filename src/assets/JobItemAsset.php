<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;

/**
 * Class JobIndexAsset
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class JobItemAsset extends AssetBundle
{
    public $sourcePath = '@zhuravljov/yii/queue/monitor/web';
    public $css = [
        'job-item.css',
    ];
    public $depends = [
        BootstrapAsset::class,
    ];
}
