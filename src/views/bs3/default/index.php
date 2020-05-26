<?php

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

\mix8872\menu\assets\MenuAsset::register($this);

$this->title = Yii::t('menu', 'Меню');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-plus']) . Yii::t('menu', ' Добавить'), ['create'], [
                        'class' => 'btn btn-success',
                        'title' => Yii::t('menu', 'Добавить новое меню')
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="<?= $model ? 'col-md-5' : 'col-md-12' ?>">
        <div class="panel">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => static function ($model) {
                            return '<u>' . Html::a($model->name, ['index', 'id' => $model->id]) . '</u>';
                        }
                    ],
                    'code',
                    'description',
                ],
            ]) ?>
        </div>
    </div>
    <?php if ($model): ?>
        <div class="col-md-7">
            <?= $this->render('update', compact('model', 'items')) ?>
        </div>
    <?php endif; ?>
</div>
