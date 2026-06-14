# Купи-Продай

![PHP Version](https://img.shields.io/badge/php-%5E8.1-7A86B8)
![MySQL Version](https://img.shields.io/badge/mysql-%5E5.7-F29221)
![Yii2 Version](https://img.shields.io/badge/Yii2-%5E2.0.45-83C933)
![Codeception Version](https://img.shields.io/badge/codeception-%5E5.0-3A97D0)

## О проекте

«Купи-Продай» — веб-сервис частных объявлений на Yii2. Пользователи публикуют объявления о покупке и продаже, ищут предложения по категориям, оставляют комментарии и общаются с авторами объявлений в онлайн-чате, работающем через Firebase.

Демонстрационная версия доступна по адресу
[https://buyandsell.sokoloff-rv.ru/](https://buyandsell.sokoloff-rv.ru/).

## Функциональность

Основные возможности, реализованные в проекте:

- регистрация и авторизация по email;
- регистрация и авторизация через VK ID;
- роли пользователя и модератора;
- создание, редактирование и удаление собственных объявлений;
- загрузка нескольких изображений объявления;
- объявления о покупке и продаже;
- категории, поиск и пагинация;
- блоки свежих и наиболее обсуждаемых объявлений;
- комментарии к объявлениям;
- разделы «Мои объявления» и «Комментарии»;
- удаление объявлений и комментариев модератором;
- онлайн-чат продавца с покупателями через Firebase Realtime Database;
- email-уведомления о непрочитанных сообщениях чата;
- страницы ошибок классов 4xx и 5xx;
- валидация форм и разграничение доступа;
- автоматические unit- и functional-тесты.

## Начало работы

Чтобы развернуть проект локально или на хостинге, выполните последовательно несколько действий:

1. Клонируйте репозиторий:

```bash
git clone https://github.com/sokoloff-rv/buy-and-sell.git buyandsell
```

2. Перейдите в директорию проекта:

```bash
cd buyandsell
```

3. Установите зависимости:

```bash
composer install
```

4. Настройте веб-сервер так, чтобы корневая директория сайта указывала на папку `web`. Директории `runtime`, `web/assets` и `web/uploads` должны быть доступны приложению для записи.

5. Создайте базу данных и файл `config/db.php` с параметрами подключения:

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

6. Создайте локальный файл параметров:

```bash
cp config/params-local.php.example config/params-local.php
```

Укажите в нем параметры своего окружения:

```php
<?php

return [
    'cookieValidationKey' => 'random-secret',
    'vkClientId' => '',
    'vkClientSecret' => '',
    'vkReturnUrl' => 'https://example.com/login/vk-auth',
    'mailerDsn' => 'smtp://user:password@smtp.example.com:587',
    'firebaseCredentialsPath' => '/path/to/service-account.json',
    'firebaseDatabaseUri' => 'https://project-id-default-rtdb.firebaseio.com',
    'firebaseWebConfig' => [
        'apiKey' => '',
        'authDomain' => '',
        'databaseURL' => '',
        'projectId' => '',
        'appId' => '',
    ],
    'siteUrl' => 'https://example.com',
];
```

Секреты, учетные данные БД и Firebase service account JSON не должны попадать в Git. Service account JSON следует хранить вне публичной директории `web`.

7. Примените миграции Yii RBAC и проекта:

```bash
php yii migrate --migrationPath=@yii/rbac/migrations --interactive=0
php yii migrate --interactive=0
```

## Онлайн-чат и уведомления

Правила доступа Firebase находятся в `firebase/database.rules.json`. После настройки Firebase CLI разверните их в Realtime Database:

```bash
GOOGLE_APPLICATION_CREDENTIALS=/path/to/service-account.json npx firebase-tools deploy --only database --project PROJECT_ID
php tests/firebase_rules_check.php
```

Email-уведомления о непрочитанных сообщениях отправляет команда:

```bash
php yii chat/notify
```

Пример запуска каждые пять минут через cron:

```cron
*/5 * * * * cd /path/to/buyandsell && /usr/bin/flock -n /tmp/buyandsell-chat-notify.lock /usr/bin/php yii chat/notify >> runtime/logs/chat-notify.log 2>&1
```

## Роли пользователей

Назначить или снять роль модератора можно консольными командами:

```bash
php yii role/moderator moderator@example.com
php yii role/revoke-moderator moderator@example.com
```

## Тестирование

Тесты используют отдельную базу данных и не должны запускаться на рабочей БД. Создайте тестовую базу данных, затем настройте подключение:

```bash
cp config/test_db-local.php.example config/test_db-local.php
```

Учетной записи СУБД необходим полный доступ к тестовой базе. Параметры также можно передать через `TEST_DB_DSN`, `TEST_DB_USER` и `TEST_DB_PASSWORD`.

Запуск проверок:

```bash
php vendor/codeception/codeception/codecept build
composer test
```

## Техническое задание

[Посмотреть техническое задание проекта](ai_docs/tz.md)
