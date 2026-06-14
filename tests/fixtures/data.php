<?php

$now = time();
$date = date('Y-m-d H:i:s', $now);
$password = password_hash('password123', PASSWORD_DEFAULT);

return [
    'users' => [
        ['id' => 1, 'name' => 'Обычный пользователь', 'email' => 'user@example.com', 'password' => $password, 'avatar' => null, 'vk_id' => null, 'created_at' => $date, 'updated_at' => $date],
        ['id' => 2, 'name' => 'Модератор', 'email' => 'moderator@example.com', 'password' => $password, 'avatar' => '/img/avatar.jpg', 'vk_id' => null, 'created_at' => $date, 'updated_at' => $date],
    ],
    'offers' => [
        ['id' => 1, 'user_id' => 1, 'title' => 'Тестовое объявление пользователя', 'description' => 'Подробное описание тестового объявления длиной более пятидесяти символов.', 'type' => 'sell', 'price' => 1500, 'created_at' => $date, 'updated_at' => $date],
        ['id' => 2, 'user_id' => 2, 'title' => 'Тестовое объявление модератора', 'description' => 'Еще одно подробное описание тестового объявления для проверки доступа.', 'type' => 'buy', 'price' => 2500, 'created_at' => $date, 'updated_at' => $date],
    ],
    'categories' => [
        ['id' => 1, 'name' => 'Тестовая категория', 'image' => '/img/blank.png'],
    ],
    'offer_categories' => [
        ['id' => 1, 'offer_id' => 1, 'category_id' => 1],
        ['id' => 2, 'offer_id' => 2, 'category_id' => 1],
    ],
    'images' => [
        ['id' => 1, 'offer_id' => 1, 'image_path' => '/img/blank.png'],
    ],
    'comments' => [
        ['id' => 1, 'user_id' => 2, 'offer_id' => 1, 'text' => 'Тестовый комментарий достаточной длины.', 'created_at' => $date, 'updated_at' => $date],
    ],
    'auth_item' => [
        ['name' => 'user', 'type' => 1, 'description' => null, 'rule_name' => null, 'data' => null, 'created_at' => $now, 'updated_at' => $now],
        ['name' => 'moderator', 'type' => 1, 'description' => null, 'rule_name' => null, 'data' => null, 'created_at' => $now, 'updated_at' => $now],
        ['name' => 'createOffer', 'type' => 2, 'description' => null, 'rule_name' => null, 'data' => null, 'created_at' => $now, 'updated_at' => $now],
        ['name' => 'editOffer', 'type' => 2, 'description' => null, 'rule_name' => null, 'data' => null, 'created_at' => $now, 'updated_at' => $now],
        ['name' => 'deleteOffer', 'type' => 2, 'description' => null, 'rule_name' => null, 'data' => null, 'created_at' => $now, 'updated_at' => $now],
        ['name' => 'deleteComment', 'type' => 2, 'description' => null, 'rule_name' => null, 'data' => null, 'created_at' => $now, 'updated_at' => $now],
    ],
    'auth_item_child' => [
        ['parent' => 'user', 'child' => 'createOffer'],
        ['parent' => 'moderator', 'child' => 'createOffer'],
        ['parent' => 'moderator', 'child' => 'editOffer'],
        ['parent' => 'moderator', 'child' => 'deleteOffer'],
        ['parent' => 'moderator', 'child' => 'deleteComment'],
    ],
    'auth_assignment' => [
        ['item_name' => 'user', 'user_id' => '1', 'created_at' => $now],
        ['item_name' => 'moderator', 'user_id' => '2', 'created_at' => $now],
    ],
];
