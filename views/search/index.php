<?php

/* @var $this yii\web\View */

$this->title = 'Поиск';
?>

<section class="search-results">
    <h1 class="visually-hidden">Результаты поиска</h1>
    <div class="search-results__wrapper">
        <p class="search-results__label">Найдено <span class="js-results"><?= $pagination->totalCount ?> публикации</span></p>    
        <ul class="search-results__list">
            <?php foreach ($offers as $offer) : ?>
                <li class="search-results__item">
                    <?= $this->render('../partials/_offerCard', ['offer' => $offer]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
</section>

<?= $this->render('../partials/_newOffers') ?>

