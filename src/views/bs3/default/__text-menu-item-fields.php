<?php

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;

?>

<div class="panel panel-default js-item item">
    <div class="panel-heading">
        <a href="<?= $item->url ?>" target="_blank" class="js-name-link"><?= $item->name ?></a>
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
        <?= $form->field($item, "[{$item->id}]name")->textInput(['class' => 'form-control name', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]url")->textInput(['class' => 'form-control url', 'required' => true]) ?>
        <?= $form->field($item, "[{$item->id}]description")->textInput(['class' => 'form-control description']) ?>
        <?= $form->field($item, "[{$item->id}]icon_class")->textInput(['class' => 'form-control icon-class']) ?>
    </div>
</div>
