<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\migrations;

use zhuravljov\yii\queue\monitor\base\Migration;

/**
 * Memory Usage
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M180412000000MemoryUsage extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn($this->env->execTableName, 'memory_usage', $this->bigInteger()->after('done_at'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn($this->env->execTableName, 'memory_usage');
    }
}
