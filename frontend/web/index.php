<?php
/**
 * HudumaLynk — Frontend web entry point
 */
defined('YII_DEBUG') or define('YII_DEBUG', (bool)($_ENV['APP_DEBUG'] ?? true));
defined('YII_ENV')   or define('YII_ENV',   $_ENV['APP_ENV'] ?? 'dev');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../config/main.php',
    file_exists(__DIR__ . '/../../common/config/main-local.php')
        ? require __DIR__ . '/../../common/config/main-local.php' : [],
    file_exists(__DIR__ . '/../config/main-local.php')
        ? require __DIR__ . '/../config/main-local.php' : []
);

(new yii\web\Application($config))->run();
