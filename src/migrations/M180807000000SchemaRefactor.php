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
class M180807000000SchemaRefactor extends Migration
{
    public function safeUp()
    {
        /* First remove auto increment column */
        $this->alterColumn($this->env->pushTableName,'id' ,$this->bigInteger());
        /* remove primary key index */
        $this->dropPrimaryKey($this->db->schema->getTableSchema($this->env->pushTableName)->primaryKey[0],$this->env->pushTableName);
        /* add new primary */
        $this->alterColumn($this->env->pushTableName,'id' ,$this->bigPrimaryKey());
        $this->alterColumn($this->env->pushTableName,'parent_id' ,$this->bigInteger());
        $this->alterColumn($this->env->pushTableName,'pushed_at' ,$this->integer()->unsigned()->notNull());
        $this->alterColumn($this->env->pushTableName,'stopped_at' , $this->integer()->unsigned());
        $this->alterColumn($this->env->pushTableName,'first_exec_id' , $this->bigInteger());
        $this->alterColumn($this->env->pushTableName,'last_exec_id' , $this->bigInteger());

        $this->renameColumn($this->env->pushTableName,'push_ttr','ttr');
        $this->alterColumn($this->env->pushTableName,'ttr' , $this->integer()->unsigned());
        $this->renameColumn($this->env->pushTableName,'push_delay','delay');
        $this->alterColumn($this->env->pushTableName,'delay' , $this->integer()->unsigned());
        $this->renameColumn($this->env->pushTableName,'push_trace_data','trace');
        $this->alterColumn($this->env->pushTableName,'trace' , $this->text());
        $this->renameColumn($this->env->pushTableName,'push_env_data','context');
        $this->alterColumn($this->env->pushTableName,'context' , $this->text());


        $this->alterColumn($this->env->execTableName,'id' ,$this->bigInteger());
        $this->dropPrimaryKey($this->db->schema->getTableSchema($this->env->execTableName)->primaryKey[0],$this->env->execTableName);
        $this->alterColumn($this->env->execTableName,'id' ,$this->bigPrimaryKey());
        $this->alterColumn($this->env->execTableName,'push_id' ,$this->bigInteger()->notNull());
        $this->alterColumn($this->env->execTableName,'worker_id' ,$this->bigInteger());
        $this->alterColumn($this->env->execTableName,'attempt' ,$this->integer()->unsigned()->notNull());

        $this->renameColumn($this->env->execTableName,'reserved_at' ,'started_at');
        $this->renameColumn($this->env->execTableName,'done_at' ,'finished_at');

        $this->alterColumn($this->env->execTableName,'started_at' ,$this->integer()->unsigned()->notNull());
        $this->alterColumn($this->env->execTableName,'finished_at' ,$this->integer()->unsigned());
        $this->alterColumn($this->env->execTableName,'memory_usage' ,$this->bigInteger()->unsigned());


        $this->alterColumn($this->env->workerTableName,'id' ,$this->bigInteger());
        $this->dropPrimaryKey($this->db->schema->getTableSchema($this->env->workerTableName)->primaryKey[0],$this->env->workerTableName);
        $this->alterColumn($this->env->workerTableName,'id' ,$this->bigPrimaryKey());
        $this->addColumn($this->env->workerTableName,'host' ,$this->string(64)->after('sender_name'));
        $this->alterColumn($this->env->workerTableName,'pid' ,$this->integer()->unsigned());
        $this->alterColumn($this->env->workerTableName,'started_at' ,$this->integer()->unsigned()->notNull());
        $this->alterColumn($this->env->workerTableName,'pinged_at' ,$this->integer()->unsigned()->notNull());
        $this->alterColumn($this->env->workerTableName,'stopped_at' ,$this->integer()->unsigned());
        $this->alterColumn($this->env->workerTableName,'finished_at' ,$this->integer()->unsigned());
        $this->alterColumn($this->env->workerTableName,'last_exec_id' ,$this->bigInteger());

        $this->dropIndex('pid',$this->env->workerTableName);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn($this->env->pushTableName,'id' ,$this->integer());
        $this->dropPrimaryKey($this->db->schema->getTableSchema($this->env->pushTableName)->primaryKey[0],$this->env->pushTableName);
        $this->alterColumn($this->env->pushTableName,'id' ,$this->primaryKey());
        $this->alterColumn($this->env->pushTableName,'parent_id' ,$this->integer());
        $this->alterColumn($this->env->pushTableName,'pushed_at' ,$this->integer()->notNull());
        $this->alterColumn($this->env->pushTableName,'stopped_at' , $this->integer());
        $this->alterColumn($this->env->pushTableName,'first_exec_id' , $this->integer());
        $this->alterColumn($this->env->pushTableName,'last_exec_id' , $this->integer());

        $this->renameColumn($this->env->pushTableName,'ttr','push_ttr');
        $this->alterColumn($this->env->pushTableName,'push_ttr' , $this->integer());
        $this->renameColumn($this->env->pushTableName,'delay','push_delay');
        $this->alterColumn($this->env->pushTableName,'push_delay' , $this->integer());
        $this->renameColumn($this->env->pushTableName,'trace','push_trace_data');
        $this->alterColumn($this->env->pushTableName,'push_trace_data' , $this->binary());
        $this->renameColumn($this->env->pushTableName,'context','push_env_data');
        $this->alterColumn($this->env->pushTableName,'push_env_data' , $this->binary());


        $this->alterColumn($this->env->execTableName,'id' ,$this->integer());
        $this->dropPrimaryKey($this->db->schema->getTableSchema($this->env->execTableName)->primaryKey[0],$this->env->execTableName);
        $this->alterColumn($this->env->execTableName,'id' ,$this->primaryKey());
        $this->alterColumn($this->env->execTableName,'push_id' ,$this->integer()->notNull());
        $this->alterColumn($this->env->execTableName,'worker_id' ,$this->integer());
        $this->alterColumn($this->env->execTableName,'attempt' ,$this->integer()->notNull());

        $this->alterColumn($this->env->execTableName,'started_at' ,$this->integer()->notNull());
        $this->alterColumn($this->env->execTableName,'finished_at' ,$this->integer());

        $this->renameColumn($this->env->execTableName,'started_at' ,'reserved_at');
        $this->renameColumn($this->env->execTableName,'finished_at' ,'done_at');

        $this->alterColumn($this->env->execTableName,'memory_usage' ,$this->bigInteger());

        $this->alterColumn($this->env->workerTableName,'id' ,$this->integer());
        $this->dropPrimaryKey($this->db->schema->getTableSchema($this->env->workerTableName)->primaryKey[0],$this->env->workerTableName);
        $this->alterColumn($this->env->workerTableName,'id' ,$this->primaryKey());
        $this->dropColumn($this->env->workerTableName,'host');
        $this->alterColumn($this->env->workerTableName,'pid' ,$this->integer());
        $this->alterColumn($this->env->workerTableName,'started_at' ,$this->integer()->notNull());
        $this->alterColumn($this->env->workerTableName,'pinged_at' ,$this->integer()->notNull());
        $this->alterColumn($this->env->workerTableName,'stopped_at' ,$this->integer());
        $this->alterColumn($this->env->workerTableName,'finished_at' ,$this->integer());
        $this->alterColumn($this->env->workerTableName,'last_exec_id' ,$this->integer());

        $this->createIndex('pid',$this->env->workerTableName,['pid']);
    }
}