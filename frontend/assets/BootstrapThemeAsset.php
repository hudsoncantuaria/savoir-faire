<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BootstrapThemeAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web/themes';
    public $css = [
        'css/bootstrap-theme.min.css',
        'css/bootstrap.min.css',
    ];
    public $js = [
        'js/bootstrap.min.js',
    ];
    public $publishOptions = [
        'only' => [
            'fonts/',
            'css/',
            'js/'
        ]
    ];
    public $depends = [
        'frontend\\assets\\FontAwesomeAsset'
    ];

}
