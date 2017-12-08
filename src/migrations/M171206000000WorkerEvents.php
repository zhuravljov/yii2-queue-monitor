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
 * Storage of worker events
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M171206000000WorkerEvents extends Migration
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
        $this->createTable($this->env->workerTableName, [
            'id' => $this->primaryKey(),
            'sender_name' => $this->string(32)->notNull(),
            'pid' => $this->integer()->notNull(),
            'started_at' => $this->integer()->notNull(),
            'finished_at' => $this->integer(),
        ], $this->tableOptions);
        $this->createIndex('pid', $this->env->workerTableName, 'pid');
        $this->createIndex('finished_at', $this->env->workerTableName, 'finished_at');

        $this->addColumn($this->env->execTableName, 'worker_id', $this->integer()->after('push_id'));
        $this->createIndex('worker_id', $this->env->execTableName, 'worker_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('worker_id', $this->env->execTableName);
        $this->dropColumn($this->env->execTableName, 'worker_id');

        $this->dropIndex('finished_at', $this->env->workerTableName);
        $this->dropIndex('pid', $this->env->workerTableName);
        $this->dropTable($this->env->workerTableName);
    }
}