<?php
/**
 * Created by PhpStorm.
 * User: Mix
 * Date: 21.11.2017
 * Time: 8:55
 */

namespace mix8872\menu\widgets;

use Yii;
use mix8872\menu\models\Menu;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class MenuWidget extends \yii\widgets\Menu
{
    /**
     * Append class to the ul element
     * @var string
     */
    public $menuClassName = '';
    /**
     * Append id to the 'ul' element
     * @var string
     */
    public $menuId = '';
    /**
     * Append class to the all 'li' elements which are not parents or descendants
     * @var string
     */
    public $itemsClassName = '';
    /**
     * Append class to the all 'a' elements in the parent elements
     * @var string
     */
    public $linkClassName = '';
    /**
     * Append class to the all 'a' elements in the descendant elements
     * @var string
     */
    public $sublinkClassName = '';
    /**
     * Append class to the all descendant 'ul' elements
     * @var string
     */
    public $submenuClassName = '';
    /**
     * Append class to the all descendant 'li' elements
     * @var string
     */
    public $subitemsClassName = '';
    /**
     * Show description
     * @var bool
     */
    public $description = false;
    /**
     * Menu code
     * @var
     */
    public $code;
    /**
     * Active 'li' element class name
     * @var string
     */
    public $activeClassName = 'active';
    /**
     * Append class to the all parent 'li' elements
     * @var string
     */
    public $parentClassName = '';
    /**
     * Menu type: classic (default) or buttons
     * @var string
     */
    public $type = 'classic';
    /**
     * Tag to wrap submenu 'ul' element
     * @var
     */
    public $submenuWrapperTag;
    /**
     * Append class to submenu wrapper (submenuWrapperTag must defined)
     * @var string
     */
    public $submenuWrapperClassName = '';
    /**
     * If false (default) then parents elements has no links and shown as 'span'
     * @var bool
     */
    public $isParentActive = false;
    /**
     * Template for item 'a' elements which are not parents
     * @var string
     */
    public $itemTemplate = '{link}';
    /**
     * Template for item 'a' elements which are parents
     * @var string
     */
    public $parentTemplate = '{link}';
    /**
     * Template for all item 'a' elements
     * @var string
     */
    public $linkTemplate = '{link}';
    /**
     * If true then current url will be excluded from menu, default false
     * @var bool
     */
    public $exceptCurrent = false;
    /**
     * Array of url's which must excluded from menu
     * @var array
     */
    public $exceptItems = [];
    /**
     * @var array
     */
    protected $itemsList = [];

    public function run()
    {
        parent::run();

        $this->route = trim(Yii::$app->request->url, '/');

        if (!$this->itemsList && $this->code) {
            if ($model = Menu::find()->where(['code' => $this->code])->one()) {
                $query = $model->getDescendants();
                if ($this->exceptCurrent) {
                    $url = '/' . trim(str_replace(Yii::$app->language, '', $this->route), '/') . '/';
                    $query->andWhere(['<>', 'url', $url]);
                }
                if ($this->exceptItems) {
                    if (is_array($this->exceptItems)) {
                        foreach ($this->exceptItems as $exceptUrl) {
                            $query->andWhere(['<>', 'url', $exceptUrl]);
                        }
                    } elseif (is_string($this->exceptItems)) {
                        $query->andWhere(['<>', 'url', $this->exceptItems]);
                    }
                }
                $this->itemsList = $query->asArray()->all();
            }
        }

        if ($this->itemsList) {
            $items = $this->itemsList;
            foreach ($this->itemsList as $i => $item) {
                if ((int)$item['type'] === Menu::TYPE_MODEL && new $item['modelClass'] instanceof ActiveRecord) {
                    $modelItems = $this->getModelItems($item);
                    array_splice($items, $i, 1, $modelItems);
                }
            }

            $items = $this->prepareItems($items, $hasActiveChild);

            switch ($this->type) {
                case 'classic':
                    return $this->renderClassic($items);
                case 'buttons':
                    return $this->renderButtons($items);
            }
        }
    }

    private function renderClassic($items)
    {
        return $this->render('classic', [
            'menuId' => $this->menuId,
            'menuClassName' => $this->menuClassName,
            'itemsClassName' => $this->itemsClassName,
            'subitemsClassName' => $this->subitemsClassName,
            'linkClassName' => $this->linkClassName,
            'sublinkClassName' => $this->sublinkClassName,
            'submenuClassName' => $this->submenuClassName,
            'description' => $this->description,
            'activeClassName' => $this->activeClassName,
            'parentClassName' => $this->parentClassName,
            'items' => $items,
            'isParentActive' => $this->isParentActive,
            'itemTemplate' => $this->itemTemplate,
            'parentTemplate' => $this->parentTemplate,
            'submenuWrapperTag' => $this->submenuWrapperTag,
            'submenuWrapperClassName' => $this->submenuWrapperClassName,
            'linkTemplate' => $this->linkTemplate
        ]);
    }

    private function renderButtons($items)
    {
        $prev = null;
        $next = null;
        foreach ($items as $key => $item) {
            if ($item['active']) {
                $prev = $key > 0 ? $items[$key - 1] : null;
                $next = $key + 1 < sizeof($items) ? $items[$key + 1] : null;
                break;
            }
        }
        return $this->render('buttons', [
            'menuId' => $this->menuId,
            'menuClassName' => $this->menuClassName,
            'itemsClassName' => $this->itemsClassName,
            'linkClassName' => $this->linkClassName,
            'submenuClassName' => $this->submenuClassName,
            'description' => $this->description,
            'activeClassName' => $this->activeClassName,
            'parentClassName' => $this->parentClassName,
            'prev' => $prev,
            'next' => $next,
        ]);
    }

    protected function isItemActive($item)
    {
        if (isset($item['url']) && !empty($item['url'])) {
            $route = Yii::getAlias($item['url']);
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            $route = trim($route, '/');
            $exRoute = explode('/', $this->route);

            if (!empty($route)) {
                switch (true) {
                    case $route === $this->route:
                    case $route === $exRoute[0]:
                        return true;
                }
            }
        }
        return false;
    }

    /**
     * Prepare the [[items]] property to remove invisible items and activate certain items.
     * @param array $items the items to be normalized.
     * @param bool $active whether there is an active child menu item.
     * @return array the normalized menu items
     */
    protected function prepareItems($items, &$active)
    {
        foreach ($items as $i => $item) {
            if (!$item['url']) {
                unset($items[$i]);
                continue;
            }
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);
                continue;
            }
            if (!isset($item['label'])) {
                $item['label'] = '';
            }

            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $items[$i]['label'] = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $hasActiveChild = false;
            if (isset($item['items'])) {
                $items[$i]['items'] = $this->normalizeItems($item['items'], $hasActiveChild);
                if (empty($items[$i]['items']) && $this->hideEmptyItems) {
                    unset($items[$i]['items']);
                    if (!isset($item['url'])) {
                        unset($items[$i]);
                        continue;
                    }
                }
            }
            if (!isset($item['active'])) {
                if ($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item)) {
                    $active = $items[$i]['active'] = true;
                } else {
                    $items[$i]['active'] = false;
                }
            } elseif ($item['active'] instanceof Closure) {
                $active = $items[$i]['active'] = call_user_func($item['active'], $item, $hasActiveChild, $this->isItemActive($item), $this);
            } elseif ($item['active']) {
                $active = true;
            }
        }

        return array_values($items);
    }

    protected function getModelItems($item)
    {
        $query = $item['modelClass']::find();
        if ($item['requestOptions']) {
            $options = preg_replace('/\s+/', ' ', $item['requestOptions']);
            preg_match('/(WHERE `?\w+`?\s?=\s?"?\w+"?+)?(\s?ORDER BY \w+\s?(ASC|DESC)?)?(\s?LIMIT \d+)?/', $options, $matches);
            array_shift($matches);
            foreach ($matches as $option) {
                $option = trim($option);
                if (stripos($option, 'W') === 0) {
                    $option = str_ireplace('WHERE ', '', $option);
                    preg_match('/(\w+)\s?=\s?(\w+)/', $option, $whereMatch);
                    array_shift($whereMatch);
                    if (count($whereMatch) === 2) {
                        $query->where([$whereMatch[0] => $whereMatch[1]]);
                    }
                    continue;
                }
                if (stripos($option, 'O') === 0) {
                    $option = str_ireplace('ORDER BY ', '', $option);
                    preg_match('/(\w+)\s?(\w+)?/', $option, $orderMatch);
                    array_shift($orderMatch);
                    if (count($orderMatch) >= 1) {
                        $query->orderBy(implode(' ', $orderMatch));
                    }
                    continue;
                }
                if (stripos($option, 'L') === 0) {
                    $option = str_ireplace('LIMIT ', '', $option);
                    preg_match('/(\d+)/', $option, $limitMatch);
                    array_shift($limitMatch);
                    if (count($limitMatch) === 1) {
                        $query->limit((int)$limitMatch[0]);
                    }
                    continue;
                }
            }
        }
        $resultItems = array();
        $modelItems = $query->all();
        $last = count($modelItems) - 1;
        foreach ($modelItems as $i => $modelItem) {
            if (strpos($item['titleAttr'], '.') && ($nameSpl = explode('.', $item['titleAttr'])) && count($nameSpl) === 2) {
                $name = $modelItem->{$nameSpl[0]}->{$nameSpl[1]};
            } else {
                $name = $modelItem->{$item['titleAttr']};
            }
            if ($item['descriptionAttr']) {
                if (strpos($item['descriptionAttr'], '.') && ($nameSpl = explode('.', $item['descriptionAttr'])) && count($nameSpl) === 2) {
                    $description = $modelItem->{$nameSpl[0]}->{$nameSpl[1]};
                } else {
                    $description = $modelItem->{$item['descriptionAttr']};
                }
            } else {
                $description = '';
            }
            $url = str_replace('{' . $item['codeAttr'] . '}', $modelItem->{$item['codeAttr']}, $item['url']);
            $resultItems[] = [
                'name' => $name,
                'url' => $url,
                'description' => $description,
                'object' => $item,
                'depth' => $item['depth'],
                'rgt' => $i === $last ? $item['rgt'] : 2,
                'lft' => $i === $last ? $item['lft'] : 1,
                'icon_class' => $item['icon_class']
            ];
        }
        return $resultItems;
    }
}
