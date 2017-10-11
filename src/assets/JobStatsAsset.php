<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\assets;

use yii\web\AssetBundle;

/**
 * Class JobStatsAsset
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class JobStatsAsset extends AssetBundle
{
    public $sourcePath = '@zhuravljov/yii/queue/monitor/web';
    public $js = [
        'job-stats.js',
    ];
    public $depends = [
        ChartJsAsset::class,
    ];
}