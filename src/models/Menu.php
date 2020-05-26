<?php

namespace mix8872\menu\models;

use Yii;
use paulzi\nestedsets\NestedSetsBehavior;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $tree
 * @property integer $type
 * @property string $url
 * @property string $description
 * @property string $name [varchar(255)]
 * @property string $code [varchar(255)]
 * @property string $icon_class [varchar(255)]
 * @property bool $is_external [tinyint(1)]
 */
class Menu extends \yii\db\ActiveRecord
{
    public $label;
    public $active;

    public const TYPE_TEXT = 0;
    public const TYPE_MODEL = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    public function behaviors()
    {
        return [
            [
                'class' => NestedSetsBehavior::class,
                'treeAttribute' => 'tree',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new MenuFind(static::class);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['url', 'name'], 'required', 'when' => function ($model) {
                return (int)$model->type === Menu::TYPE_TEXT;
            }, 'whenClient' => "function (attribute) {
                var spl = attribute.id.split('-');            
                var type = $('#menu-' + spl[1] + '-type').val();
                return type == " . self::TYPE_TEXT . ";
            }"],
            [['url', 'modelClass', 'codeAttr', 'titleAttr'], 'required', 'when' => function ($model) {
                return (int)$model->type === Menu::TYPE_MODEL;
            }, 'whenClient' => "function (attribute) {
                var spl = attribute.id.split('-');            
                var type = $('#menu-' + spl[1] + '-type').val();
                return type == " . self::TYPE_MODEL . ";
            }"],
            [['tree', 'lft', 'rgt', 'depth', 'type'], 'integer'],
            [['name', 'url', 'description', 'code', 'icon_class', 'modelClass', 'codeAttr', 'titleAttr', 'descriptionAttr', 'requestOptions'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['code'], 'match', 'pattern' => '/^[a-z_0-9-]+/i', 'message' => Yii::t('menu', 'Код может содержать только буквы a-z, - и _')],
            [['name', 'url', 'description', 'icon_class', 'modelClass', 'codeAttr', 'titleAttr', 'descriptionAttr', 'requestOptions'], 'filter', 'filter' => 'trim', 'skipOnArray' => true]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lft' => 'lft',
            'rgt' => 'rgt',
            'depth' => 'depth',
            'tree' => 'tree',
            'type' => Yii::t('menu', 'Тип'),
            'name' => Yii::t('menu', 'Название'),
            'code' => Yii::t('menu', 'Код'),
            'url' => Yii::t('menu', 'Url'),
            'description' => Yii::t('menu', 'Описание'),
            'icon_class' => Yii::t('menu', 'Класс иконки'),
            'modelClass' => Yii::t('menu', 'Класс модели'),
            'codeAttr' => Yii::t('menu', 'Атрибут url'),
            'titleAttr' => Yii::t('menu', 'Атрибут заголовка'),
            'descriptionAttr' => Yii::t('menu', 'Атрибут описания'),
            'requestOptions' => Yii::t('menu', 'Параметры выборки (where, order by)')
        ];
    }
}
