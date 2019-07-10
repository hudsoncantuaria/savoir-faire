<?php

error_reporting(E_ALL);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../common/config/bootstrap.php');
require(__DIR__ . '/../frontend/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../common/config/main.php'),
    require(__DIR__ . '/../common/config/main-local.php'),
    require(__DIR__ . '/../frontend/config/main.php'),
    require(__DIR__ . '/../frontend/config/main-local.php')
);

$application = new yii\web\Application($config);
$application->on(yii\web\Application::EVENT_BEFORE_REQUEST, function(yii\base\Event $event) {
    /*
    $event->sender->response->on(yii\web\Response::EVENT_BEFORE_SEND, function($e) {
        ob_start("ob_gzhandler");
    });
    
    $event->sender->response->on(yii\web\Response::EVENT_AFTER_SEND, function($e) {
        ob_end_flush();
    });
    */
});

$application->run();
