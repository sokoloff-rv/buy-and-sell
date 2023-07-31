<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $offer->title;
?>

<section class="ticket">
    <div class="ticket__wrapper">
        <h1 class="visually-hidden">Карточка объявления</h1>
        <div class="ticket__content">
            <div class="ticket__img">
                <img src="<?= Html::encode($offer->images[0]->image_path) ?>" alt="Изображение товара">
            </div>
            <div class="ticket__info">
                <h2 class="ticket__title"><?= Html::encode($offer->title) ?></h2>
                <div class="ticket__header">
                    <p class="ticket__price"><span class="js-sum"><?= Html::encode((round($offer->price, 0))) ?></span> ₽</p>
                    <p class="ticket__action"><?= Html::encode($offer->label) ?></p>
                </div>
                <div class="ticket__desc">
                    <p><?= Html::encode($offer->description) ?></p>
                </div>
                <div class="ticket__data">
                    <p>
                        <b>Дата добавления:</b>
                        <span><?= Yii::$app->formatter->asDate($offer->created_at, 'long') ?></span>
                    </p>
                    <p>
                        <b>Автор:</b>
                        <?= Html::encode($offer->user->name) ?>
                    </p>
                    <p>
                        <b>Контакты:</b>
                        <a href="mailto:<?= Html::encode($offer->user->email) ?>"><?= Html::encode($offer->user->email) ?></a>
                    </p>
                </div>
                <ul class="ticket__tags">
                    <?php foreach ($offer->categories as $category) : ?>
                        <li>
                            <a href="<?= Url::to(['category/view', 'id' => $category->id]) ?>" class="category-tile category-tile--small">
                                <span class="category-tile__image">
                                    <img src="<?= $category->image ?>"  alt="Иконка категории">
                                </span>
                                <span class="category-tile__label"><?= Html::encode($category->name) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
