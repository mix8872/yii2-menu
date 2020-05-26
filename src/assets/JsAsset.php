<?php
namespace mix8872\menu\assets;

use Yii;

class JsAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@mix8872/menu/assets';
    public $js = [
		'js/jquery.mjs.nestedSortable.js',
		'js/menu.js'
    ];

    public $depends = [
        MenuAsset::class
    ];
}
