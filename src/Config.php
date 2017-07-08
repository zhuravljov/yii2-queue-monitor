<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor;

use yii\base\Object;
use yii\db\Connection;
use yii\di\Instance;

/**
 * Class Config
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Config extends Object
{
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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
    }

}