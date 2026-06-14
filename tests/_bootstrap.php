<?php

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$db = require __DIR__ . '/../config/test_db.php';
$prefix = $db['tablePrefix'];
if (!preg_match('/^[a-zA-Z0-9_]+$/', $prefix)) {
    throw new RuntimeException('Некорректный префикс тестовых таблиц.');
}
$connection = new PDO($db['dsn'], $db['username'], $db['password']);
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = [
    'auth_assignment',
    'auth_item_child',
    'auth_item',
    'auth_rule',
    'comments',
    'images',
    'offer_categories',
    'categories',
    'offers',
    'users',
];

$connection->exec('SET FOREIGN_KEY_CHECKS=0');
foreach ($tables as $table) {
    $connection->exec("DROP TABLE IF EXISTS `{$prefix}{$table}`");
}
foreach (array_reverse($tables) as $table) {
    $connection->exec("CREATE TABLE `{$prefix}{$table}` LIKE `{$table}`");
}
$connection->exec('SET FOREIGN_KEY_CHECKS=1');

$fixtures = require __DIR__ . '/fixtures/data.php';
foreach ($fixtures as $table => $rows) {
    foreach ($rows as $row) {
        $columns = array_keys($row);
        $columnSql = implode(', ', array_map(static fn (string $column): string => "`{$column}`", $columns));
        $valueSql = implode(', ', array_fill(0, count($columns), '?'));
        $statement = $connection->prepare("INSERT INTO `{$prefix}{$table}` ({$columnSql}) VALUES ({$valueSql})");
        $statement->execute(array_values($row));
    }
}
