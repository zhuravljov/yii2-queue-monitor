<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\migrations;

use yii\db\Query;
use yii\helpers\Json;
use zhuravljov\yii\queue\monitor\base\Migration;

/**
 * Job Json Data
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class M180310000001JobJsonData extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameColumn(
            $this->env->pushTableName,
            'job_object',
            'job_data'
        );

        $time = $this->beginCommand("update {$this->env->pushTableName}");
        $this->compact = true;
        $query = (new Query())->from($this->env->pushTableName);
        foreach ($query->each(1000, $this->db) as $row) {
            if (is_resource($row['job_data'])) {
                $row['job_data'] = stream_get_contents($row['job_data']);
            }
            $job = unserialize($row['job_data']);
            $data = [];
            foreach (get_object_vars($job) as $property => $value) {
                if ($property !== '__PHP_Incomplete_Class_Name') {
                    $data[$property] = $this->serializeData($value);
                }
            }
            $this->update(
                $this->env->pushTableName,
                ['job_data' => Json::encode($data)],
                ['id' => $row['id']]
            );
        }
        $this->compact = false;
        $this->endCommand($time);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return false;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    private function serializeData($data)
    {
        if (is_object($data)) {
            $result = ['=class=' => get_class($data)];
            foreach (get_object_vars($data) as $property => $value) {
                $result[$property] = $this->serializeData($value);
            }
            return $result;
        }
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->serializeData($value);
            }
        }
        return $data;
    }
}
