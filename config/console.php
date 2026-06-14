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
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'mailer' => $mailer,
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'hostInfo' => $params['siteUrl'],
            'baseUrl' => '',
            'rules' => [
                'offers/<id:\d+>' => 'offers/index',
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'imageStorage' => [
            'class' => 'app\components\ImageStorage',
        ],
        'firebase' => [
            'class' => 'app\components\FirebaseComponent',
            'credentialsPath' => $params['firebaseCredentialsPath'],
            'databaseUri' => $params['firebaseDatabaseUri'],
        ],
    ],
    'params' => $params,

];

if (YII_ENV_DEV) {

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',

    ];
}

return $config;
