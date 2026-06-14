<?php

$this->title = 'Поиск';

$foundCount = $pagination->totalCount;
$foundWord = 'Найдено';
$offerWord = Yii::$app->i18n->format('{n, plural, =1{объявление} one{объявление} few{объявления} many{объявлений} other{объявлений}}', ['n' => $foundCount], 'ru_RU');
?>

<section class="search-results">
    <h1 class="visually-hidden">Результаты поиска</h1>
    <div class="search-results__wrapper">
        <?php if (!$foundCount) : ?>
            <div class="search-results__message">
                <p>Не найдено ни одного объявления</p>
            </div>
        <?php else : ?>
            <p class="search-results__label">
                <?= $foundWord ?> <span class="js-results"><?= $foundCount ?> <?= $offerWord ?></span>
            </p>
            <ul class="search-results__list">
                <?php foreach ($offers as $offer) : ?>
                    <li class="search-results__item">
                        <?= $this->render('../partials/_offerCard', ['offer' => $offer]) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
</section>

<?= $this->render('../partials/_newOffers', ['offers' => $newOffers]) ?>
