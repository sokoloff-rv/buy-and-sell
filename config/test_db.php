<?php

$db = require __DIR__ . '/db.php';

$db['dsn'] = getenv('TEST_DB_DSN') ?: $db['dsn'];
$db['username'] = getenv('TEST_DB_USER') ?: $db['username'];
$db['password'] = getenv('TEST_DB_PASSWORD') ?: $db['password'];
$db['tablePrefix'] = getenv('TEST_DB_PREFIX') ?: 'test_';
$db['enableSchemaCache'] = false;

return $db;
