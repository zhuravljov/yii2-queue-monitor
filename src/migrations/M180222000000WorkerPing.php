<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\migrations;

use yii\db\Query;
use zhuravljov\yii\queue\monitor\base\Migration;

/**
 * Storage of worker events
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M180222000000WorkerPing extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->env->workerTableName,
            'pinged_at',
            $this->integer()->notNull()->after('started_at')
        );

        $time = $this->beginCommand("update {$this->env->workerTableName}");
        $this->compact = true;
        $query = (new Query())->from($this->env->workerTableName);
        foreach ($query->all($this->db) as $row) {
            $this->update(
                $this->env->workerTableName,
                ['pinged_at' => $row['finished_at'] ?: time()],
                ['id' => $row['id']]
            );
        }
        $this->compact = false;
        $this->endCommand($time);

        $this->addColumn(
            $this->env->workerTableName,
            'stopped_at',
            $this->integer()->after('pinged_at')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn($this->env->workerTableName, 'stopped_at');
        $this->dropColumn($this->env->workerTableName, 'pinged_at');
    }
}
