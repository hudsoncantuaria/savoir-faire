<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        'batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'overwrite' => true,
            'modelNamespace' => 'common\\models',
            'modelQueryNamespace' => 'common\\models\\query',
            'crudControllerNamespace' => 'frontend\\controllers',
            'crudSearchModelNamespace' => 'common\\models\\search',
            'crudViewPath' => 'frontend/views',
            'crudPathPrefix' => '',
            'crudTidyOutput' => true,
            'crudAccessFilter' => true,
            'crudProviders' => [
                'schmunk42\\giiant\\generators\\crud\\providers\\core\\optsProvider',
            ],
            'tablePrefix' => '',
        /* 'tables' => [
          'app_profile',
          ] */
        ]
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
