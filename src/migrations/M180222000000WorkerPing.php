<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\migrations;

use yii\db\Migration;
use yii\db\Query;
use zhuravljov\yii\queue\monitor\Env;

/**
 * Storage of worker events
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M180222000000WorkerPing extends Migration
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
        $this->addColumn(
            $this->env->workerTableName,
            'pinged_at',
            $this->integer()->notNull()->after('started_at')
        );

        $query = (new Query())->from($this->env->workerTableName);
        foreach ($query->all() as $row) {
            $this->update(
                $this->env->workerTableName,
                ['pinged_at' => $row['finished_at'] ?: time()],
                ['id' => $row['id']]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn($this->env->workerTableName, 'pinged_at');
    }
}
