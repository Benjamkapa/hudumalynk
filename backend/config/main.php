<?php
use yii\helpers\ArrayHelper;

$params = ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id'                  => 'hudumalynk-backend',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute'        => 'site/index',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => $_ENV['APP_KEY'] ?? 'fallback-key-backend',
            'baseUrl'   => '',
        ],
        'user' => [
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl'        => ['/site/login'],
        ],
        'session' => [
            'name' => 'hudumalynk-backend-session',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [[
                'class'   => 'yii\log\FileTarget',
                'levels'  => ['error', 'warning'],
                'logFile' => '@backend/runtime/logs/app.log',
            ]],
        ],
        'errorHandler' => ['errorAction' => 'site/error'],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules' => [
                ''                                   => 'site/index',
                'login'                              => 'site/login',
                'register'                           => 'site/register',
                'forgot-password'                    => 'site/forgot-password',
                'reset-password/<token>'             => 'site/reset-password',
                'logout'                             => 'site/logout',
                'error'                              => 'site/error',
                // Admin
                'admin/dashboard'                    => 'admin/dashboard',
                'admin/users'                        => 'admin/users',
                'admin/users/<id:\d+>'               => 'admin/view-user',
                'admin/providers'                    => 'admin/providers',
                'admin/providers/<id:\d+>/approve'   => 'admin/approve-provider',
                'admin/providers/<id:\d+>/suspend'   => 'admin/suspend-provider',
                'admin/providers/<id:\d+>'           => 'admin/view-provider',
                'admin/orders'                       => 'admin/orders',
                'admin/orders/<id:\d+>'              => 'admin/view-order',
                'admin/payments'                     => 'admin/payments',
                'admin/categories'                   => 'admin/categories',
                'admin/categories/create'            => 'admin/create-category',
                'admin/plans'                        => 'admin/plans',
                'admin/plans/create'                 => 'admin/create-plan',
                'admin/plans/<id:\d+>/update'        => 'admin/update-plan',
                'admin/commission'                   => 'admin/commission',
                'admin/reports'                      => 'admin/reports',
                'admin/settings'                     => 'admin/settings',
                // Provider area
                'dashboard'                          => 'provider/dashboard',
                'my-listings'                        => 'provider/listings',
                'my-listings/create'                 => 'provider/create-listing',
                'my-listings/<id:\d+>/update'        => 'provider/edit-listing',
                'my-listings/<id:\d+>/delete'        => 'provider/delete-listing',
                'my-orders'                          => 'provider/orders',
                'my-orders/<id:\d+>/accept'          => 'provider/accept-order',
                'my-orders/<id:\d+>/complete'        => 'provider/complete-order',
                'subscription'                       => 'provider/subscription',
                'subscription/purchase/<planId:\d+>' => 'provider/subscribe-plan',
                'earnings'                           => 'provider/earnings',
                'profile'                            => 'provider/profile',
                'provider/dashboard'                 => 'provider/dashboard',
                'provider/listings'                  => 'provider/listings',
                'provider/create-listing'            => 'provider/create-listing',
                'provider/edit-listing'              => 'provider/edit-listing',
                'provider/delete-listing'            => 'provider/delete-listing',
                'provider/orders'                    => 'provider/orders',
                'provider/accept-order'              => 'provider/accept-order',
                'provider/complete-order'            => 'provider/complete-order',
                'provider/subscription'              => 'provider/subscription',
                'provider/subscribe-plan/<planId:\d+>' => 'provider/subscribe-plan',
                'provider/earnings'                  => 'provider/earnings',
                'provider/profile'                   => 'provider/profile',
'provider/reviews'                  => 'provider/reviews',
                'provider/orders/<id:\d+>'           => 'provider/order-view',
            ],
        ],
    ],
    'params' => $params,
];

