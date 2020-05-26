<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model mix8872\admin\models\Menu */

$this->title = Yii::t('menu', 'Редактирование меню: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'Меню'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<?= $this->render('_form', [
    'model' => $model,
    'items' => $items
]) ?>
