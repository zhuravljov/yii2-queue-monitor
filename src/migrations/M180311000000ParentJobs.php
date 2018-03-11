<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\migrations;

use zhuravljov\yii\queue\monitor\base\Migration;

/**
 * Parent Jobs
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M180311000000ParentJobs extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn($this->env->pushTableName, 'parent_id', $this->integer()->after('id'));
        $this->createIndex('parent_id', $this->env->pushTableName, 'parent_id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('parent_id', $this->env->pushTableName);
        $this->dropColumn($this->env->pushTableName, 'parent_id');
    }
}
