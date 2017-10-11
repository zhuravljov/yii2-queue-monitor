<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\assets;

use yii\web\AssetBundle;

/**
 * Class ChartJsAsset
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class ChartJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/chartjs/dist';
    public $js = [
        'Chart.bundle.min.js',
    ];
}