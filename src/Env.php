<?php
/**
 * @link      https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license   http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor;

use yii\base\Object;
use yii\caching\Cache;
use yii\db\Connection;
use yii\di\Instance;
use zhuravljov\yii\queue\monitor\records\ExecRecord;
use zhuravljov\yii\queue\monitor\records\PushRecord;

/**
 * Class Env
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Env extends Object
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
    public $recordModelClass = PushRecord::class;
    
    /**
     * @var string
     */
    public $execModelClass = ExecRecord::class;
    
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
     * @return \yii\db\ActiveRecord|PushRecord
     */
    public function recordModel()
    {
        return new $this->recordModelClass;
    }
    
    /**
     * @return \yii\db\ActiveRecord|ExecRecord
     */
    public function execModel()
    {
        return new $this->execModelClass;
    }
}