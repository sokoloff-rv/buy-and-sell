<?php

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$db = require __DIR__ . '/../config/test_db.php';

$console = new yii\console\Application([
    'id' => 'buyandsell-test-setup',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'db' => $db,
        'authManager' => ['class' => yii\rbac\DbManager::class],
        'cache' => ['class' => yii\caching\FileCache::class],
    ],
]);

ob_start();
$rbacResult = $console->runAction('migrate/up', ['migrationPath' => '@yii/rbac/migrations', 'interactive' => 0]);
$appResult = $console->runAction('migrate/up', ['interactive' => 0]);
$migrateOutput = ob_get_clean();
if ($rbacResult !== 0 || $appResult !== 0) {
    fwrite(STDERR, $migrateOutput);
    throw new RuntimeException('Не удалось применить миграции тестовой базы.');
}

$connection = Yii::$app->db;
$tables = [
    'comments', 'images', 'offer_categories', 'categories', 'offers', 'users',
    'auth_assignment', 'auth_item_child', 'auth_item', 'auth_rule',
];

$connection->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
foreach ($tables as $table) {
    $connection->createCommand()->delete($table)->execute();
}
$connection->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();

$fixtures = require __DIR__ . '/fixtures/data.php';
foreach ($fixtures as $table => $rows) {
    foreach ($rows as $row) {
        $connection->createCommand()->insert($table, $row)->execute();
    }
}
