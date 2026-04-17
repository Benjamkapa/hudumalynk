<?php
use yii\helpers\ArrayHelper;

return [
    'id'                  => 'hudumalynk-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [[
                'class'   => 'yii\log\FileTarget',
                'levels'  => ['error', 'warning'],
                'logFile' => '@console/runtime/logs/app.log',
            ]],
        ],
    ],
    'controllerMap' => [
        'migrate' => [
            'class'          => 'yii\console\controllers\MigrateController',
            'migrationPath'  => '@console/migrations',
            'migrationTable' => '{{%migrations}}',
        ],
        'rbac' => [
            'class' => 'console\controllers\RbacController',
        ],
        'subscription' => [
            'class' => 'console\controllers\SubscriptionController',
        ],
        'seed' => [
            'class' => 'console\controllers\SeedController',
        ],
    ],
    'params' => require __DIR__ . '/../../common/config/params.php',
];
