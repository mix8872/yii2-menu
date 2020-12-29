<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<ul <?= $menuId ? 'id="'.$menuId.'"' : '' ?> <?= $menuClassName ? 'class="'.$menuClassName.'"' : '' ?>>
    <?php if (isset($items)):
        $prevItem = NULL;
        $level = 0;
        $linkAttr = [];
		$itemLink = null;
        foreach($items as $key => $item) {

            $liClass = '';

            if ($linkClassName) {
                $linkAttr['class'] = $linkClassName;
            }
            if ($item['depth'] > 1 && $sublinkClassName) {
                $linkAttr['class'] = $sublinkClassName;
            }
            if ($itemsClassName) {
                $liClass = $itemsClassName;
            }
            if ($item['depth'] > 1 && $subitemsClassName) {
                $liClass = $subitemsClassName;
            }
            if ($item['active']) {
                $liClass = empty($liClass) ? $activeClassName : $liClass . ' ' . $activeClassName;
            }
            if (!is_null($prevItem) && $prevItem['depth'] < $item['depth']) {
                $level ++;
            } elseif (!is_null($prevItem) && $prevItem['depth'] > $item['depth']) {
                $step = $prevItem['depth'] - $item['depth'];
                $level -= $step;
                echo str_repeat('</ul></li>', $step);
            }
			$itemLink = strpos($item['url'], '//') !== false ? $item['url'] : [$item['url']];
            $itemTitle = $item['name'];
            if (is_callable($linkTitleTemplate)) {
                $itemTitle = $linkTitleTemplate($item['name'], $itemLink, $linkAttr);
            } elseif (is_string($linkTitleTemplate)) {
                $itemTitle = str_replace('{title}', $itemTitle, $linkTitleTemplate);
            }

            if ($item['rgt'] - $item['lft'] === 1):  // if not parent?>
                <li <?= !empty($liClass) ? "class='$liClass'" : '' ?>>
                    <?php if ($item['icon_class']) {
                        $icon = '<i class="' . $item['icon_class'] . '"></i>';

                        $link = Html::a("{$icon} " . Html::tag('span', $itemTitle), $itemLink, $linkAttr);
                        if (is_callable($linkTemplate)) {
                            $link = $linkTemplate($itemTitle, $itemLink, $linkAttr, $icon);
                        } elseif (is_string($linkTemplate)) {
                            $link = str_replace('{link}', $link, $linkTemplate);
                        }
                    } else {
                        $link = Html::a($itemTitle, $itemLink, $linkAttr);
                        if (is_callable($linkTemplate)) {
                            $link = $linkTemplate($itemTitle, $itemLink, $linkAttr);
                        } elseif (is_string($linkTemplate)) {
                            $link = str_replace('{link}', $link, $linkTemplate);
                        }
                    }
                    echo str_replace('{link}', $link, $itemTemplate);
                    ?>
                    <?php if($description): ?>
                        <div class="menu-item-description">
                            <?= $item['description'] ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php else:// if parent
                if ($parentClassName) {
                    $liClass = empty($liClass) ? $parentClassName : $liClass . ' ' . $parentClassName;
                }
                if ($isParentActive) {
                    $parentLinkAttr = array_merge(['href' => Url::to($itemLink)], $linkAttr);
                    $tag = 'a';
                } else {
					$parentLinkAttr = $linkAttr;
					$tag = 'span';
				}
                $arrowDown = '<div class="arrow-down"></div>';
                ?>
                <li <?= !empty($liClass) ? "class='$liClass'" : '' ?>>
                    <?php if ($item['icon_class']) {
                        $icon = '<i class="' . $item['icon_class'] . '"></i>';
                        $link = Html::tag($tag, "{$icon} " . Html::tag('span', $item['name']) . $arrowDown, $parentLinkAttr);
                        if (is_callable($linkTemplate)) {
                            $link = $linkTemplate($item, $itemLink, $linkAttr);
                        } elseif (is_string($linkTemplate)) {
                            $link = str_replace('{link}', $link, $linkTemplate);
                        }
                    } else {
                        $link = Html::tag($tag, $item['name'] . $arrowDown, $parentLinkAttr);
                        if (is_callable($linkTemplate)) {
                            $link = $linkTemplate($item, $itemLink, $linkAttr);
                        } elseif (is_string($linkTemplate)) {
                            $link = str_replace('{link}', $link, $linkTemplate);
                        }
                    }
                    echo str_replace('{link}', $link, $parentTemplate);
                    ?>
                    <?php if($description): ?>
                        <div class="menu-item-description">
                            <?= $item['description'] ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($submenuWrapperTag) {
                        echo "<$submenuWrapperTag class='$submenuWrapperClassName'>";
                    } ?>
                    <ul <?= $submenuClassName ? 'class="'.$submenuClassName.'"' : '' ?>>
            <?php
            endif;
            $prevItem = $item;
        }
        if($level > 0) {
            echo str_repeat('</ul>' . ($submenuWrapperTag ? "</$submenuWrapperTag>" : '') . '</li>', $level);
        }

    endif; ?>
</ul>
