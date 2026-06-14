<?php

$db = require __DIR__ . '/db.php';

$db['dsn'] = 'mysql:host=localhost;dbname=buyandsell_test';
$db['tablePrefix'] = '';
$db['enableSchemaCache'] = false;

$localTestDb = __DIR__ . '/test_db-local.php';
if (is_file($localTestDb)) {
    $db = array_merge($db, require $localTestDb);
}

$db['dsn'] = getenv('TEST_DB_DSN') ?: $db['dsn'];
$db['username'] = getenv('TEST_DB_USER') ?: $db['username'];
$db['password'] = getenv('TEST_DB_PASSWORD') ?: $db['password'];

return $db;
