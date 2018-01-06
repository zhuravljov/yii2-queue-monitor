<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\migrations;

use yii\db\Migration;
use zhuravljov\yii\queue\monitor\Env;

/**
 * Storage of job events
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M170620000000EventStorage extends Migration
{
    /**
     * @var string
     */
    public $tableOptions;
    /**
     * @var Env
     */
    protected $env;

    /**
     * @param Env $env
     * @inheritdoc
     */
    public function __construct(Env $env, $config = [])
    {
        $this->env = $env;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable($this->env->pushTableName, [
            'id' => $this->primaryKey(),
            'sender_name' => $this->string(32)->notNull(),
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
        $this->createIndex('job_uid', $this->env->pushTableName, ['sender_name', 'job_uid']);
        $this->createIndex('first_exec_id', $this->env->pushTableName, 'first_exec_id');
        $this->createIndex('last_exec_id', $this->env->pushTableName, 'last_exec_id');

        $this->createTable($this->env->execTableName, [
            'id' => $this->primaryKey(),
            'push_id' => $this->integer()->notNull(),
            'attempt' => $this->integer()->notNull(),
            'reserved_at' => $this->integer()->notNull(),
            'done_at' => $this->integer(),
            'error' => $this->text(),
            'retry' => $this->boolean(),
        ], $this->tableOptions);
        $this->createIndex('push_id', $this->env->execTableName, 'push_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable($this->env->execTableName);
        $this->dropTable($this->env->pushTableName);
    }
}
