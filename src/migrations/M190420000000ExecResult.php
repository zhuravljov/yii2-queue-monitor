<?php

namespace zhuravljov\yii\queue\monitor\migrations;

use zhuravljov\yii\queue\monitor\base\Migration;

/**
 * Exec Result
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M190420000000ExecResult extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn($this->env->execTableName, 'result_data', $this->binary()->after('error'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn($this->env->execTableName, 'result_data');
    }
}
