<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\base;

use zhuravljov\yii\queue\monitor\Env;

/**
 * Class Migration
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
abstract class Migration extends \yii\db\Migration
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
    public function binary($length = null)
    {
        if ($this->db->driverName === 'mysql') {
            return $this->db->schema->createColumnSchemaBuilder('longblob');
        }
        return parent::binary($length);
    }

    /**
     * @inheritdoc
     */
    public function text()
    {
        if ($this->db->driverName === 'mysql') {
            return $this->db->schema->createColumnSchemaBuilder('longtext');
        }
        return parent::text();
    }
}
