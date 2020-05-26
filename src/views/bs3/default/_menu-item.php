<?php
if (!isset($form)) {
    $form = new \yii\widgets\ActiveForm();
}

if (!isset($isNewItem)) {
    $isNewItem = false;
}

if (!$isParent): ?>
    <li id="menu_<?= $item->id ?>" data-id="<?= $item->id ?>">
        <?php if ((int)$item->type === \mix8872\menu\models\Menu::TYPE_TEXT): ?>
            <?= $this->render('__text-menu-item-fields', compact('item', 'form', 'isNewItem')) ?>
        <?php else : ?>
            <?= $this->render('__model-menu-item-fields', compact('item', 'form', 'isNewItem')) ?>
        <?php endif; ?>
    </li>
<?php else: ?>
    <?= "<li id=\"menu_{$item->id}\" data-id=\"{$item->id}\">" ?>
    <?php if ((int)$item->type === \mix8872\menu\models\Menu::TYPE_TEXT): ?>
        <?= $this->render('__text-menu-item-fields', compact('item', 'form', 'isNewItem')) ?>
    <?php else : ?>
        <?= $this->render('__model-menu-item-fields', compact('item', 'form', 'isNewItem')) ?>
    <?php endif; ?>
    <?= '<ol>' ?>
<?php endif; ?>
