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
class FontAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web/themes';
    public $css = [
        'https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800,800italic|Kalam:700,400'
    ];
    public $depends = [
    ];

}
