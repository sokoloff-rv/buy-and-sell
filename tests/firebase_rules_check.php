<?php

require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

$params = require __DIR__ . '/../config/params.php';
$database = (new Factory())
    ->withServiceAccount($params['firebaseCredentialsPath'])
    ->withDatabaseUri($params['firebaseDatabaseUri'])
    ->createDatabase();
$auth = (new Factory())
    ->withServiceAccount($params['firebaseCredentialsPath'])
    ->createAuth();

$offerId = 'rules-test-' . bin2hex(random_bytes(4));
$buyerId = 'rules-buyer';
$sellerId = 'rules-seller';
$outsiderId = 'rules-outsider';
$dialogPath = "chats/{$offerId}/{$buyerId}";

$request = static function (string $method, string $url, ?array $body = null): array {
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $body === null ? null : json_encode($body),
    ]);
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);

    return [$status, json_decode((string) $response, true)];
};

$idToken = static function (string $uid) use ($auth, $params, $request): string {
    [, $response] = $request(
        'POST',
        'https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken?key=' . $params['firebaseWebConfig']['apiKey'],
        ['token' => $auth->createCustomToken($uid)->toString(), 'returnSecureToken' => true]
    );

    return $response['idToken'] ?? throw new RuntimeException('Не удалось обменять custom token.');
};

$assertStatus = static function (int $expected, array $result, string $message): void {
    if ($result[0] !== $expected) {
        throw new RuntimeException("{$message}: ожидался HTTP {$expected}, получен {$result[0]}");
    }
};

try {
    $database->getReference($dialogPath . '/meta')->set([
        'sellerId' => $sellerId,
        'buyerId' => $buyerId,
        'offerId' => $offerId,
        'updatedAt' => round(microtime(true) * 1000),
    ]);
    $baseUrl = rtrim($params['firebaseDatabaseUri'], '/') . '/' . $dialogPath;
    $buyerToken = $idToken($buyerId);
    $sellerToken = $idToken($sellerId);
    $outsiderToken = $idToken($outsiderId);

    $assertStatus(200, $request('PUT', "{$baseUrl}/messages/test.json?auth={$buyerToken}", [
        'senderId' => $buyerId,
        'recipientId' => $sellerId,
        'text' => 'Проверка правил доступа',
        'createdAt' => round(microtime(true) * 1000),
        'read' => false,
        'notified' => false,
    ]), 'Покупатель не смог отправить сообщение');
    $assertStatus(401, $request('GET', "{$baseUrl}.json?auth={$outsiderToken}"), 'Третий пользователь получил доступ');
    $assertStatus(401, $request('PATCH', "{$baseUrl}/messages/test.json?auth={$buyerToken}", [
        'notified' => true,
    ]), 'Клиент смог изменить notified');
    $assertStatus(200, $request('GET', "{$baseUrl}.json?auth={$sellerToken}"), 'Продавец не смог прочитать диалог');
    $assertStatus(200, $request('PATCH', "{$baseUrl}/messages/test.json?auth={$sellerToken}", [
        'read' => true,
    ]), 'Получатель не смог отметить сообщение прочитанным');

    echo "Firebase Rules: OK\n";
} finally {
    $database->getReference('chats/' . $offerId)->remove();
    $auth->deleteUsers([$buyerId, $sellerId, $outsiderId]);
}
