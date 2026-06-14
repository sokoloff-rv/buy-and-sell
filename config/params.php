<?php

$params = [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    'cookieValidationKey' => '',
    'vkClientId' => '',
    'vkClientSecret' => '',
    'vkReturnUrl' => 'https://buyandsell.sokoloff-rv.ru/login/vk-auth',
    'mailerDsn' => '',
    'firebaseCredentialsPath' => '',
    'firebaseDatabaseUri' => '',
    'firebaseWebConfig' => [],
    'siteUrl' => 'https://buyandsell.sokoloff-rv.ru',
];

$localParams = __DIR__ . '/params-local.php';
if (is_file($localParams)) {
    $params = array_merge($params, require $localParams);
}

return $params;
