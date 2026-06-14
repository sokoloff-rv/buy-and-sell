<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'ru-RU',
    'components' => [
        'db' => $db,
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'main/index',
                'register' => 'register/index',
                'login' => 'login/index',
                'login/vk-email' => 'login/vk-email',
                'search' => 'search/index',
                'offers/add' => 'offers/add',
                'offers/edit/<id:\d+>' => 'offers/edit',
                'offers/category/<id:\d+>' => 'offers/category',
                'offers/<id:\d+>' => 'offers/index',
                'my/comments' => 'my/comments',
                'my/delete-comment/<id:\d+>' => 'my/delete-comment',
                'my' => 'my/index',
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'search' => [
            'class' => 'app\components\SearchComponent',
        ],
        'imageStorage' => [
            'class' => 'app\components\ImageStorage',
            'uploadPath' => '@runtime/test-uploads',
            'uploadUrl' => '/test-uploads',
        ],
        'errorHandler' => [
            'errorAction' => 'main/error',
        ],
    ],
    'params' => $params,
];
