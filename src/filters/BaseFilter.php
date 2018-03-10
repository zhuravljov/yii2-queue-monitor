<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\filters;

use Yii;
use yii\base\Model;
use zhuravljov\yii\queue\monitor\Env;

/**
 * Class BaseFilter
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class BaseFilter extends Model
{
    /**
     * @var Env
     */
    protected $env;

    /**
     * @param Env $env
     * @param array $config
     */
    public function __construct(Env $env, $config = [])
    {
        $this->env = $env;
        parent::__construct($config);
    }

    public static function ensure()
    {
        /** @var static $filter */
        $filter = Yii::createObject(get_called_class());
        $filter->load(Yii::$app->request->queryParams) && $filter->validate();
        $filter->storeParams();
        return $filter;
    }

    /**
     * @return array
     */
    public static function restoreParams()
    {
        return Yii::$app->session->get(get_called_class(), []);
    }

    public function storeParams()
    {
        $params = [];
        foreach ($this->attributes as $attribute => $value) {
            if ($value !== null && $value !== '') {
                $params[$attribute] = $value;
            }
        }
        Yii::$app->session->set(get_called_class(), $params);
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }
}
