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
class M171221000000WorkerLastExec extends Migration
{
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
        $this->addColumn($this->env->workerTableName, 'last_exec_id', $this->integer());
        $this->createIndex('last_exec_id', $this->env->workerTableName, 'last_exec_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('last_exec_id', $this->env->workerTableName);
        $this->dropColumn($this->env->workerTableName, 'last_exec_id');
    }
}