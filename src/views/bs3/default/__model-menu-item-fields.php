<?php

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;

?>

<div class="panel panel-default js-item item">
    <div class="panel-heading">
        <a href="<?= $item->url ?>" target="_blank" class="js-name-link"><?= $item->modelClass ?> (model)</a>
        <div class="pull-right">
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
    <div class="panel-body js-details collapse<?= $isNewItem ? ' show' : '' ?>" id="menu-details-<?= $item->id ?>">
        <?= Html::activeHiddenInput($item, "[{$item->id}]type") ?>
        <?= $form->field($item, "[{$item->id}]modelClass")->textInput(['class' => 'form-control name', 'placeholder' => '\\common\\models\SomeModel', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]url")->textInput(['class' => 'form-control url', 'placeholder' => '/news/{' . Yii::t('menu', 'атрибут_url') . '}', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]codeAttr")->textInput(['class' => 'form-control description', 'placeholder' => 'id', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]titleAttr")->textInput(['class' => 'form-control description', 'placeholder' => 'title', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]descriptionAttr")->textInput(['class' => 'form-control description', 'placeholder' => 'description']) ?>
        <?= $form->field($item, "[{$item->id}]requestOptions")->textInput(['class' => 'form-control description', 'placeholder' => 'WHERE STATUS = 1 ORDER BY ID DESC']) ?>
    </div>
</div>
