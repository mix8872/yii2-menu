<?php

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;

?>

<div class="card js-item item border-secondary text-secondary">
    <div class="card-header bg-light">
        <a href="<?= $item->url ?>" target="_blank" class="js-name-link"><?= $item->modelClass ?> (model)</a>
        <div class="float-right">
            <span class="js-more more fa fa-chevron-<?= $isNewItem ? 'up' : 'down' ?>" data-toggle="collapse"
                  data-target="#menu-details-<?= $item->id ?>"></span>
            <?= Html::a(FAS::icon('trash'),
                ['delete-item', 'id' => $item->id],
                [
                    'class' => 'delete js-delete',
                    'title' => Yii::t('menu', 'Удалить'),
                    'data-id' => $item->id
                ]) ?>
        </div>
    </div>
    <div class="card-body js-details collapse<?= $isNewItem ? ' show' : '' ?>" id="menu-details-<?= $item->id ?>">
        <?= Html::activeHiddenInput($item, "[{$item->id}]type") ?>
        <?= $form->field($item, "[{$item->id}]url")->textInput(['class' => 'form-control url', 'placeholder' => '/news/' . Yii::t('menu', 'атрибут_url') . '', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]icon_class")->textInput(['class' => 'form-control']) ?>
        <?= $form->field($item, "[{$item->id}]modelClass")->textInput(['class' => 'form-control name', 'placeholder' => '\\common\\models\SomeModel', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]codeAttr")->textInput(['class' => 'form-control', 'placeholder' => 'id', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]titleAttr")->textInput(['class' => 'form-control', 'placeholder' => 'title', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]descriptionAttr")->textInput(['class' => 'form-control description', 'placeholder' => 'description']) ?>
        <?= $form->field($item, "[{$item->id}]requestOptions")->textInput(['class' => 'form-control description', 'placeholder' => 'WHERE status = 1 ORDER BY id DESC LIMIT 5']) ?>
    </div>
</div>
