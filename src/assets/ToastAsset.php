<?php
namespace mix8872\menu\assets;

class ToastAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/bower-asset/jquery-toast-plugin';
    public $css = [
		'dist/jquery.toast.min.css',
    ];
    public $js = [
		'dist/jquery.toast.min.js',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
    ];
}
