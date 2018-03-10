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
 * Storage Refactoring
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M180310000000StorageRefactoring extends Migration
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
    public function safeUp()
    {
        $this->renameColumn($this->env->pushTableName, 'ttr', 'push_ttr');
        $this->renameColumn($this->env->pushTableName, 'delay', 'push_delay');
        $this->addColumn($this->env->pushTableName, 'push_trace_data', $this->binary()->after('push_delay'));
        $this->addColumn($this->env->pushTableName, 'push_env_data', $this->binary()->after('push_trace_data'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn($this->env->pushTableName, 'push_env_data');
        $this->dropColumn($this->env->pushTableName, 'push_trace_data');
        $this->renameColumn($this->env->pushTableName, 'push_delay', 'delay');
        $this->renameColumn($this->env->pushTableName, 'push_ttr', 'ttr');
    }
}
