<?php
use yii\helpers\ArrayHelper;

$params = ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id'                  => 'hudumalynk-frontend',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute'        => 'browse/index',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => $_ENV['APP_KEY'] ?? 'fallback-key-frontend',
            'baseUrl'   => '',
        ],
        'user' => [
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl'        => ['/auth/login'],
        ],
        'session' => [
            'name' => 'hudumalynk-session',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [[
                'class'   => 'yii\log\FileTarget',
                'levels'  => ['error', 'warning'],
                'logFile' => '@frontend/runtime/logs/app.log',
            ]],
        ],
        'errorHandler' => ['errorAction' => 'site/error'],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules' => [
                ''                              => 'browse/index',
                // Back-office routes belong to the backend app; redirect if they hit frontend.
                'dashboard'                     => 'site/backend-redirect',
                'admin/<path:.+>'              => 'site/backend-redirect',
                'provider/dashboard'           => 'site/backend-redirect',
                'provider/listings'            => 'site/backend-redirect',
                'provider/create-listing'      => 'site/backend-redirect',
                'provider/edit-listing'        => 'site/backend-redirect',
                'provider/delete-listing'      => 'site/backend-redirect',
                'provider/orders'              => 'site/backend-redirect',
                'provider/accept-order'        => 'site/backend-redirect',
                'provider/complete-order'      => 'site/backend-redirect',
                'provider/subscription'        => 'site/backend-redirect',
                'provider/subscribe-plan/<planId:\d+>' => 'site/backend-redirect',
                'provider/earnings'            => 'site/backend-redirect',
                'provider/profile'             => 'site/backend-redirect',
                'provider/reviews'             => 'site/backend-redirect',
                'about'                         => 'site/about',
                'contact'                       => 'site/contact',
                'error'                         => 'site/error',
                'login'                         => 'auth/login',
                'logout'                        => 'auth/logout',
                'register'                      => 'auth/register',
                'join'                          => 'site/join',
                'join-as-provider'              => 'auth/register-provider',
                'forgot-password'               => 'auth/forgot-password',
                'reset-password/<token>'        => 'auth/reset-password',
                'browse'                        => 'browse/index',
                'browse/<slug:[a-z0-9-]+>'      => 'browse/category',
                'listing/<id:\d+>'              => 'listing/view',
                'listing/<id:\d+>/<slug:[a-z0-9-]+>' => 'listing/view',
                'provider/<id:\d+>'             => 'provider/profile',
                'provider/<id:\d+>/<slug:[a-z0-9-]+>' => 'provider/profile',
                'order/create'                  => 'order/create',
                'order/pay/<id:\d+>'            => 'order/pay',
                'order/mpesa-callback'          => 'order/mpesa-callback',
                'order/flw-callback'            => 'order/flw-callback',
                'orders'                        => 'order/index',
                'orders/<id:\d+>'               => 'order/view',
                'account'                       => 'account/index',
                'account/profile'               => 'account/profile',
                'account/password'              => 'account/password',
                'account/notifications'         => 'account/notifications',
                // Ajax / utility
                'api/currency-rate'             => 'api/currency-rate',
            ],
        ],
    ],
    'params' => $params,
];
