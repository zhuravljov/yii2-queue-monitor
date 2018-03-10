<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\filters;

use zhuravljov\yii\queue\monitor\records\WorkerQuery;
use zhuravljov\yii\queue\monitor\records\WorkerRecord;

/**
 * Class WorkerFilter
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class WorkerFilter extends BaseFilter
{
    /**
     * @return WorkerQuery
     */
    public function search()
    {
        return WorkerRecord::find();
    }
}
