<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\migrations;

use zhuravljov\yii\queue\monitor\base\Migration;

/**
 * Storage Schema
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M180807000000Schema extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable($this->env->pushTableName, [
            'id' => $this->bigPrimaryKey(),
            'parent_id' => $this->bigInteger(),
            'sender_name' => $this->string(32)->notNull(),
            'job_uid' => $this->string(32)->notNull(),
            'job_class' => $this->string()->notNull(),
            'job_data' => $this->binary()->notNull(),
            'ttr' => $this->integer()->unsigned()->notNull(),
            'delay' => $this->integer()->unsigned()->notNull(),
            'trace' => $this->text(),
            'context' => $this->text(),
            'pushed_at' => $this->integer()->unsigned()->notNull(),
            'stopped_at' => $this->integer()->unsigned(),
            'first_exec_id' => $this->bigInteger(),
            'last_exec_id' => $this->bigInteger(),
        ]);
        $this->createIndex('ind_qp_parent_id', $this->env->pushTableName, 'parent_id');
        $this->createIndex('ind_qp_job_uid', $this->env->pushTableName, ['sender_name', 'job_uid']);
        $this->createIndex('ind_qp_job_class', $this->env->pushTableName, 'job_class');
        $this->createIndex('ind_qp_first_exec_id', $this->env->pushTableName, 'first_exec_id');
        $this->createIndex('ind_qp_last_exec_id', $this->env->pushTableName, 'last_exec_id');

        $this->createTable($this->env->execTableName, [
            'id' => $this->bigPrimaryKey(),
            'push_id' => $this->bigInteger()->notNull(),
            'worker_id' => $this->bigInteger(),
            'attempt' => $this->integer()->unsigned()->notNull(),
            'started_at' => $this->integer()->unsigned()->notNull(),
            'finished_at' => $this->integer()->unsigned(),
            'memory_usage' => $this->bigInteger()->unsigned(),
            'error' => $this->text(),
            'retry' => $this->boolean(),
        ]);
        $this->createIndex('ind_qe_push_id', $this->env->execTableName, 'push_id');
        $this->createIndex('ind_qe_worker_id', $this->env->execTableName, 'worker_id');

        $this->createTable($this->env->workerTableName, [
            'id' => $this->bigPrimaryKey(),
            'sender_name' => $this->string(32)->notNull(),
            'host' => $this->string(64),
            'pid' => $this->integer()->unsigned(),
            'started_at' => $this->integer()->unsigned()->notNull(),
            'pinged_at' => $this->integer()->unsigned()->notNull(),
            'stopped_at' => $this->integer()->unsigned(),
            'finished_at' => $this->integer()->unsigned(),
            'last_exec_id' => $this->bigInteger(),
        ]);
        $this->createIndex('ind_qw_finished_at', $this->env->workerTableName, 'finished_at');
        $this->createIndex('ind_qw_last_exec_id', $this->env->workerTableName, 'last_exec_id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->env->workerTableName);
        $this->dropTable($this->env->execTableName);
        $this->dropTable($this->env->pushTableName);
    }
}
