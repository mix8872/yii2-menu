<?php

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use voskobovich\tree\manager\widgets\nestable\Nestable;

/* @var $this yii\web\View */
/* @var $model mix8872\menu\models\Menu */
/* @var $form yii\widgets\ActiveForm */

\mix8872\menu\assets\JsAsset::register($this);
?>


<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="panel panel-default item js-item">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8">
                <h3 class="page-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="col-md-4">
                <div class="pull-right margin-top-3">
                    <?= Html::tag('span', '', [
                        'class' => 'more js-more fa fa-chevron-down',
                        'title' => Yii::t('menu', 'Редактировать'),
                        'data-toggle' => 'collapse',
                        'data-target' => '#menu-details'
                    ]) ?>
                    <?= Html::a(FAS::icon('chevron-left'), ['index'], [
                        'class' => 'btn btn-warning margin-left-2',
                        'title' => Yii::t('menu', 'Отмена')
                    ]) ?>
                    <?= Html::submitButton(FAS::icon('save'), [
                        'class' => 'btn btn-success margin-left-2',
                        'title' => Yii::t('menu', 'Сохранить')
                    ]) ?>
                    <?= Html::a(FAS::icon('trash'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger margin-left-2',
                        'title' => Yii::t('menu', 'Удалить меню'),
                        'data-confirm' => Yii::t('menu', 'Вы действительно хотите удалить меню?')
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-body collapse js-details" id="menu-details">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6 col-sm-12">
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
</div>
<?php if (!$model->isNewRecord): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="page-title-box">
                <?= Html::a(FAS::icon('plus'),
                    '#',
                    [
                        'class' => 'btn btn-success mt-2',
                        'title' => Yii::t('menu', 'Добавить пункт меню'),
                        'data' => [
                            'toggle' => 'modal',
                            'target' => '#menu-item-modal'
                        ],

                    ]) ?>
            </div>
        </div>
        <div class="col-md-12">
            <ol id="menu_items-container" class="sortable" data-id=" <?= $model->id ?>">
                <?php if (isset($items)): ?>
                    <?php
                    $prevItem = NULL;
                    $level = 0;
                    foreach ($items as $item) {
                        if ($prevItem && $prevItem->rgt > $item->lft) {
                            $level++;
                        } elseif ($prevItem && $prevItem->rgt + 1 < $item->lft) {
                            $step = $item->lft - ($prevItem->rgt + 1);
                            $level -= $step;
                            echo str_repeat('</ol></li>', $step);
                        }
                        ?>
                        <?= $this->render('_menu-item', [
                            'isParent' => $item->rgt - $item->lft !== 1,
                            'item' => $item,
                            'form' => $form
                        ]) ?>
                        <?php
                        $prevItem = $item;
                    }
                    if ($level > 0) {
                        echo str_repeat('</ol></li>', $level);
                    }
                    ?>
                <?php endif; ?>
            </ol>
        </div>
    </div>
<?php endif; ?>
<?php ActiveForm::end(); ?>

<div class="modal fade" id="menu-item-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close margin-top-3" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title"><?= Yii::t('menu', 'Тип элемента меню') ?></h3>
            </div>
            <div class="modal-body">
                <div class="btn-group btn-group-justified">
                    <?= Html::a(FAS::icon('link') . ' ' . Yii::t('menu', 'Простая ссылка'),
                        ['add-item', 'id' => $model->id, 'type' => \mix8872\menu\models\Menu::TYPE_TEXT],
                        [
                            'class' => 'js-add-menu-item btn btn-success',
                            'title' => Yii::t('menu', 'Ссылка указывается вручную'),

                        ]) ?>
                    <?= Html::a(FAS::icon('database') . ' ' . Yii::t('menu', 'Из модели'),
                        ['add-item', 'id' => $model->id, 'type' => \mix8872\menu\models\Menu::TYPE_MODEL],
                        [
                            'class' => 'js-add-menu-item btn btn-success',
                            'title' => Yii::t('menu', 'Список ссылок берется из записей модели'),

                        ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$url = Url::to(['move-item']);
$deleteConfirmMsg = Yii::t('menu', 'Удалить пункт меню и его дочерние пункты?');
$js = <<<JS
$(document).ready(function(){
        new document.menu({
        updateUrl: '$url',
        deleteConfirmMsg: '$deleteConfirmMsg'
        });
    });
JS;
$this->registerJs($js);
?>

