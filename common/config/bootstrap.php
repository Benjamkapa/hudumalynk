<?php
/**
 * Common bootstrap — loads .env and sets up Yii class map
 */

// Load .env file if it exists
$dotenvPath = dirname(__DIR__, 2);
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
}

Yii::setAlias('@common', dirname(__DIR__) . '/common');
Yii::setAlias('@frontend', dirname(__DIR__) . '/frontend');
Yii::setAlias('@backend', dirname(__DIR__) . '/backend');
Yii::setAlias('@console', dirname(__DIR__) . '/console');
Yii::setAlias('@uploads', dirname(__DIR__, 2) . '/' . ($_ENV['UPLOAD_PATH'] ?? 'frontend/web/uploads'));
