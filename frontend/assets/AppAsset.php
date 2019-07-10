<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/themes';

    public $css = [
        'css/plugins/datatables.min.css',
        'css/style.css',
        'css/custom.css?v=1.0.2',
    ];
    public $js = [
        'js/main.js',
        'js/plugins/moment.js',
        'js/plugins/bootstrap-datetimepicker.min.js',
        'js/plugins/typeahead.bundle.js',
        'js/plugins/datatables.min.js',
        'js/plugins/dataTables.buttons.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'frontend\assets\FontAsset',
        'frontend\assets\BootstrapThemeAsset',
    ];
}
