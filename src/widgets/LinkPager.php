<?php
/**
 * @link https://github.com/zhuravljov/yii2-queue-monitor
 * @copyright Copyright (c) 2017 Roman Zhuravlev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace zhuravljov\yii\queue\monitor\widgets;

use yii\bootstrap\Html;

/**
 * Class LinkPager
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class LinkPager extends \yii\widgets\LinkPager
{
    /**
     * @var string
     */
    public $layout = '<div class="pull-right">{sizer}</div> {pager} <div class="clearfix"></div>';
    /**
     * @var int[]
     */
    public $sizes = [10, 20, 50];
    /**
     * @var array
     */
    public $sizerOptions = ['class' => 'pagination'];
    /**
     * @var array
     */
    public $sizerItemOptions = [];
    /**
     * @var array
     */
    public $sizerLinkOptions = [];
    /**
     * @var string
     */
    public $activeSizeCssClass = 'active';

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }

        return strtr($this->layout, [
            '{pager}' => $this->renderPageButtons(),
            '{sizer}' => $this->renderSizeButtons(),
        ]);
    }

    /**
     * @return string
     */
    protected function renderSizeButtons()
    {
        $buttons = [];
        foreach ($this->sizes as $size) {
            $limits = $this->pagination->pageSizeLimit;
            if (isset($limits[0], $limits[1]) && ($size < $limits[0] || $size > $limits[1])) {
                continue;
            }
            $options = $this->sizerItemOptions;
            if ($size == $this->pagination->pageSize) {
                Html::addCssClass($options, $this->activeSizeCssClass);
            }
            $linkOptions = $this->sizerLinkOptions;
            $buttons[] = Html::tag('li', Html::a($size, $this->pagination->createUrl(0, $size), $linkOptions), $options);
        }

        return Html::tag('ul', join("\n", $buttons), $this->sizerOptions);
    }
}