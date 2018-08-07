<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor;

use yii\base\BaseObject;
use yii\caching\Cache;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;

/**
 * Class Env
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Env extends BaseObject
{
    /**
     * @var Cache|array|string
     */
    public $cache = 'cache';
    /**
     * @var Connection|array|string
     */
    public $db = 'db';
    /**
     * @var string
     */
    public $pushTableName = '{{%queue_push}}';
    /**
     * @var string
     */
    public $execTableName = '{{%queue_exec}}';
    /**
     * @var string
     */
    public $workerTableName = '{{%queue_worker}}';
    /**
     * @var int
     */
    public $workerPingInterval = 15;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->cache = Instance::ensure($this->cache, Cache::class);
        $this->db = Instance::ensure($this->db, Connection::class);
    }

    /**
     * @return bool
     */
    public function canListenWorkerLoop()
    {
        return !!$this->workerPingInterval;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        if ($this->db->driverName === 'mysql') {
            $pair = (new Query())
                ->from('information_schema.PROCESSLIST')
                ->where('[[ID]] = CONNECTION_ID()')
                ->limit(1)
                ->select('HOST')
                ->scalar();
            return substr($pair, 0, strrpos($pair, ':'));
        }

        return '127.0.0.1'; // By default
    }
}
