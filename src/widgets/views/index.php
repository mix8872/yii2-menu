<?php
use yii\helpers\Html;
?>
<ul <?= $menuId ? 'id="'.$menuId.'""' : '' ?> <?= $menuClassName ? 'class="'.$menuClassName.'""' : '' ?>>
    <?php if (isset($items)):
        $prevItem = NULL;
        $level = 0;
        foreach($items as $key => $item) {

            $liClass = '';
            if ($itemsClassName || $item['active']) {
                $liClass = 'class="' . ($itemsClassName ? $itemsClassName . ' ' : '') . ($item['active'] ? $activeClassName : '') . '"';
            }
            if (!is_null($prevItem) && $prevItem['rgt'] > $item['lft']) {
                $level ++;
            } elseif (!is_null($prevItem) && $prevItem['rgt'] + 1 < $item['lft']) {
                $step = $item['lft'] - ($prevItem['rgt'] + 1);
                $level -= $step;
                echo str_repeat('</ul></li>', $step);
            }

            if ($item['rgt'] - $item['lft'] == 1):  ?>
                <li <?= $liClass ?>>
                    <?php $icon = $item['icon_class'] ? '<i class="' . $item['icon_class'] . '"></i>' : ''; ?>
                    <?= Html::a($icon . Html::tag('span', $item['name']), [$item['url']]) ?>
                    <?php if($description): ?>
                        <div class="menu-item-description">
                            <?= $item['description'] ?>
                        </div>
                    <?php endif; ?>
                </li>

            <?php else://parent?>

            <li <?= $itemsClassName ? 'class="'.$itemsClassName.'""' : '' ?>>
                <?= Html::a($item['name'],[$item['url']]) ?>
                <?php if($description): ?>
                    <div class="menu-item-description">
                        <?= $item['description'] ?>
                    </div>
                <?php endif; ?>
                <ul <?= $submenuClassName ? 'class="'.$submenuClassName.'""' : '' ?>>
            <?php
            endif;
            $prevItem = $item;
        }
        if($level > 0) {
            echo str_repeat('</ul></li>', $level);
        }

    endif; ?>
</ul>
