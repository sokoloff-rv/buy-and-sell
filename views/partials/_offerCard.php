<?php

use yii\helpers\Html;
?>

<div class="ticket-card">
    <div class="ticket-card__img">
        <?= Html::img($offer->previewImage, ['alt' => 'Изображение товара']) ?>
    </div>
    <div class="ticket-card__info">
        <span class="ticket-card__label"><?= Html::encode($offer->label) ?></span>
        <div class="ticket-card__categories">
            <?php foreach ($offer->categories as $category) : ?>
                <?= Html::a(Html::encode($category->name), ['offers/category', 'id' => $category->id]) ?>
            <?php endforeach; ?>
        </div>
        <div class="ticket-card__header">
            <h3 class="ticket-card__title">
                <?= Html::a(Html::encode($offer->title), !empty($showDeleteButton) ? ['/offers/edit', 'id' => $offer->id] : ['/offers/index', 'id' => $offer->id]) ?>
            </h3>
            <p class="ticket-card__price">
                <span class="js-sum"><?= Html::encode(round($offer->price, 0)) ?></span> ₽
            </p>
        </div>
        <div class="ticket-card__desc">
            <p><?= Html::encode($offer->announcement) ?></p>
        </div>
    </div>
    <?php if (isset($showDeleteButton) && $showDeleteButton): ?>
        <?= Html::beginForm(['/offers/delete', 'id' => $offer->id], 'post') ?>
        <?= Html::submitButton('Удалить', ['class' => 'ticket-card__del js-delete']) ?>
        <?= Html::endForm() ?>
    <?php endif; ?>
</div>
