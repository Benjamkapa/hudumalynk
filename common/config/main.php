<?php
/**
 * Common configuration shared by frontend, backend, and console
 */
return [
    'name'       => 'HudumaLynk',
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'timeZone'   => 'Africa/Nairobi',
    'language'   => 'en-KE',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'db' => [
            'class'     => 'yii\db\Connection',
            'dsn'       => 'mysql:host=' . ($_ENV['DB_HOST'] ?? '127.0.0.1') . ';port=' . ($_ENV['DB_PORT'] ?? '3306') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'hudumalynk'),
            'username'  => $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'enableSchemaCache' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mailer' => [
            'class'         => 'yii\swiftmailer\Mailer',
            'viewPath'      => '@common/mail',
            'useFileTransport' => ($_ENV['APP_ENV'] ?? 'development') === 'development',
            'transport' => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
                'username'   => $_ENV['MAIL_USERNAME'] ?? '',
                'password'   => $_ENV['MAIL_PASSWORD'] ?? '',
                'port'       => $_ENV['MAIL_PORT'] ?? 587,
                'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            ],
            'messageConfig' => [
                'from' => [$_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@hudumalynk.com' => $_ENV['MAIL_FROM_NAME'] ?? 'HudumaLynk'],
            ],
        ],
        // Custom components
        'mpesa' => [
            'class' => 'common\components\MpesaService',
        ],
        'sms' => [
            'class' => 'common\components\NotificationService',
        ],
        'currency' => [
            'class' => 'common\components\CurrencyService',
        ],
    ],
    'params' => require __DIR__ . '/params.php',
];
