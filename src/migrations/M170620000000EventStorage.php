<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\migrations;

use yii\db\Migration;

/**
 * Class M20170620000000QueueEvent
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M170620000000EventStorage extends Migration
{
    public $pushTableName = '{{%queue_push}}';
    public $execTableName = '{{%queue_exec}}';
    public $tableOptions;

    public function up()
    {
        $this->createTable($this->pushTableName, [
            'id' => $this->primaryKey(),
            'sender' => $this->string(32)->notNull(),
            'job_uid' => $this->string(32)->notNull(),
            'job_class' => $this->string()->notNull(),
            'job_object' => $this->binary()->notNull(),
            'ttr' => $this->integer()->notNull(),
            'delay' => $this->integer()->notNull(),
            'pushed_at' => $this->integer()->notNull(),
            'stopped_at' => $this->integer(),
            'first_exec_id' => $this->integer(),
            'last_exec_id' => $this->integer(),
        ], $this->tableOptions);
        $this->createIndex('job_uid', $this->pushTableName, ['sender', 'job_uid']);
        $this->createIndex('first_exec_id', $this->pushTableName, 'first_exec_id');
        $this->createIndex('last_exec_id', $this->pushTableName, 'last_exec_id');

        $this->createTable($this->execTableName, [
            'id' => $this->primaryKey(),
            'push_id' => $this->integer()->notNull(),
            'attempt' => $this->integer()->notNull(),
            'reserved_at' => $this->integer()->notNull(),
            'done_at' => $this->integer(),
            'error' => $this->text(),
            'retry' => $this->boolean(),
        ], $this->tableOptions);
        $this->createIndex('push_id', $this->execTableName, 'push_id');
    }

    public function down()
    {
        $this->dropTable($this->execTableName);
        $this->dropTable($this->pushTableName);
    }
}