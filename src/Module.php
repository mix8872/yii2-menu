<?php

namespace mix8872\menu;

use Yii;

/**
 * user-like module definition class
 */
class Module extends \yii\base\Module
{
    public const EVENT_AFTER_CREATE = 'menuAfterCreate';
    public const EVENT_AFTER_DELETE = 'menuAfterDelete';
    public const EVENT_AFTER_DELETE_ITEM = 'menuAfterDeleteItem';
    public const EVENT_AFTER_ADD_ITEM = 'menuAfterAddItem';
    public const EVENT_AFTER_SORT = 'menuAfterSort';
    public const EVENT_AFTER_UPDATE = 'menuAfterUpdate';

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'mix8872\menu\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (isset(Yii::$app->params['bsVersion']) && Yii::$app->params['bsVersion'] === '4.x') {
            $this->setViewPath("@mix8872/menu/views/bs4");
        } else {
            $this->setViewPath("@mix8872/menu/views/bs3");
        }

        $this->registerTranslations();
    }

    /**
     * Register translation for module
     */
    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['menu'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'ru-RU',
            'basePath' => '@vendor/mix8872/yii2-menu/src/messages',
        ];

    }
}
