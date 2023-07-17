<?php

/* @var $this yii\web\View */

$this->title = 'Главная страница';
?>

<section class="categories-list">
    <h1>Главная страница</h1>

    <?php
    if (!Yii::$app->user->isGuest) {
        $userId = Yii::$app->user->id;
        echo "Выполнена авторизация пользователя с id  $userId";
    } else {
        echo "Пользователь не авторизован";
    }
    ?>
</section>
