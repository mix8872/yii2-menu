<?php

use yii\helpers\Html;

?>
<div <?= $menuClassName ? "class='$menuClassName'" : '' ?>>
    <?php if ($prev): ?>
        <?= Html::a('<img src="/img/ui-icon/arrow-left.svg" class="img-svg">
            <span>' . Yii::t('menu', 'Назад') . ': ' . $prev['name'] . '</span>', [$prev['url']], ['class' => ($itemsClassName ? $itemsClassName : '')]) ?>
    <?php endif; ?>
    <?php if ($next): ?>
        <?= Html::a('<span>' . Yii::t('menu', 'Далее') . ': ' . $next['name'] . '</span>
            <img src="/img/ui-icon/arrow-right.svg" class="img-svg">', [$next['url']], ['class' => ($itemsClassName ? $itemsClassName : '')]) ?>
    <?php endif; ?>
</div>
