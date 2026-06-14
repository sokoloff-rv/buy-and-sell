<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$mailer = [
    'class' => \yii\symfonymailer\Mailer::class,
    'viewPath' => '@app/mail',
    'useFileTransport' => !YII_ENV_PROD && empty($params['mailerDsn']),
];
if (!empty($params['mailerDsn'])) {
    $mailer['transport'] = ['dsn' => $params['mailerDsn']];
}

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'main/error',
        ],
        'mailer' => $mailer,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'main/index',
                'register' => 'register/index',
                'login' => 'login/index',
                'login/vk' => 'login/vk',
                'login/vk-auth' => 'login/vk-auth',
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
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'vkid' => [
                    'class' => 'app\components\VKID',
                    'clientId' => $params['vkClientId'],
                    'clientSecret' => $params['vkClientSecret'],
                    'returnUrl' => $params['vkReturnUrl'],
                    'scope' => 'email',
                ],
            ],
        ],
        'search' => [
            'class' => 'app\components\SearchComponent',
        ],
        'imageStorage' => [
            'class' => 'app\components\ImageStorage',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',

    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',

    ];
}

return $config;
