<?php

/* @var $this yii\web\View */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Мои объявления';
?>

<section class="tickets-list">
    <h2 class="visually-hidden">Самые новые предложения</h2>
    <div class="tickets-list__wrapper">
        <div class="tickets-list__header">
            <a href="<?= Url::to(['offers/add']) ?>" class="tickets-list__btn btn btn--big"><span>Новая публикация</span></a>
        </div>
        <ul>
            <?php foreach ($offers as $offer) : ?>
                <li class="tickets-list__item js-card">
                    <?= $this->render('../partials/_offerCard', ['offer' => $offer, 'showDeleteButton' => true]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
