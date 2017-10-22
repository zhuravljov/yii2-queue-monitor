<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\data\Pagination;
use yii\helpers\Html;

/**
 * Class LinkSizer
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class LinkSizer extends Widget
{
    /**
     * @var Pagination
     */
    public $pagination;
    /**
     * @var int[]
     */
    public $sizes = [10, 20, 50];
    /**
     * @var array
     */
    public $options = ['class' => 'pagination'];
    /**
     * @var array
     */
    public $itemOptions = [];
    /**
     * @var array
     */
    public $linkOptions = [];
    /**
     * @var string
     */
    public $activeCssClass = 'active';

    /**
     * Initializes the sizer.
     */
    public function init()
    {
        if ($this->pagination === null) {
            throw new InvalidConfigException('The "pagination" property must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->renderButtons();
    }

    /**
     * @return string
     */
    protected function renderButtons()
    {
        $buttons = [];
        foreach ($this->sizes as $size) {
            $limits = $this->pagination->pageSizeLimit;
            if (isset($limits[0], $limits[1]) && ($size < $limits[0] || $size > $limits[1])) {
                continue;
            }
            $options = $this->itemOptions;
            if ($size == $this->pagination->pageSize) {
                Html::addCssClass($options, $this->activeCssClass);
            }
            $link = Html::a($size, $this->pagination->createUrl(0, $size), $this->linkOptions);
            $buttons[] = Html::tag('li', $link, $options);
        }

        return Html::tag('ul', join("\n", $buttons), $this->options);
    }
}