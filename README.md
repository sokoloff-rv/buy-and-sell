# Купи-Продай

Сервис объявлений на Yii 2.

## Требования

- PHP 8.1 или новее
- MySQL 5.7 или новее
- Composer
- доступ на запись к `runtime`, `web/assets` и `web/uploads`

## Установка

```bash
composer install
cp config/params-local.php.example config/params-local.php
```

Создайте `config/db.php`:

```php
<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=buyandsell',
    'username' => 'buyandsell',
    'password' => '',
    'charset' => 'utf8',
];
```

Заполните локальные параметры в `config/params-local.php`, затем примените миграции:

```bash
php yii migrate --migrationPath=@yii/rbac/migrations --interactive=0
php yii migrate --interactive=0
```

Секреты и учетные данные нельзя добавлять в Git. Локальные DB, SMTP и VK ID параметры исключены через `.gitignore`.

## Production

Для production задайте `YII_ENV=prod`, надежный `cookieValidationKey`, корректный `vkReturnUrl` и SMTP DSN:

```php
<?php

return [
    'cookieValidationKey' => 'random-secret',
    'vkClientId' => '',
    'vkClientSecret' => '',
    'vkReturnUrl' => 'https://example.com/login/vk-auth',
    'mailerDsn' => 'smtp://user:password@smtp.example.com:587',
];
```

В production файловый transport писем отключен. Ошибки и предупреждения записываются в `runtime/logs/app.log`, пользователю детали исключений не показываются.

## Роли

```bash
php yii role/moderator moderator@example.com
php yii role/revoke-moderator moderator@example.com
```

## Тесты

Из-за ограничений хостинга тесты по умолчанию используют изолированные таблицы с префиксом `test_` в доступной MySQL-базе. Перед каждым запуском они пересоздаются из актуальной production-схемы и наполняются fixtures; рабочие таблицы не изменяются.

Для отдельной тестовой базы задайте `TEST_DB_DSN`, `TEST_DB_USER`, `TEST_DB_PASSWORD` и при необходимости `TEST_DB_PREFIX`.

```bash
php vendor/codeception/codeception/codecept build
php vendor/codeception/codeception/codecept run unit,functional
```

Тесты покрывают валидацию, helpers, публичные страницы, доступы, `403/404/405`, CSRF POST-формы удаления, экранирование пользовательского HTML и основной сценарий удаления объявления.
