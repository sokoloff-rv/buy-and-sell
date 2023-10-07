<?php

/* @var $this yii\web\View */

$this->title = 'Поиск';

$foundCount = $pagination->totalCount;
$foundWord = Yii::$app->i18n->format('{n, plural, =1{Найдена} one{Найдена} few{Найдено} many{Найдено} other{Найдено}}', ['n' => $foundCount], 'ru_RU');
$publicationWord = Yii::$app->i18n->format('{n, plural, =1{публикация} one{публикация} few{публикации} many{публикаций} other{публикаций}}', ['n' => $foundCount], 'ru_RU');
?>

<section class="search-results">
    <h1 class="visually-hidden">Результаты поиска</h1>
    <div class="search-results__wrapper">
        <?php if (!$foundCount) : ?>
            <div class="search-results__message">
                <p>Не найдено <br>ни&nbsp;одной публикации</p>
            </div>
        <?php else : ?>
            <p class="search-results__label">
                <?= $foundWord ?> <span class="js-results"><?= $foundCount ?> <?= $publicationWord ?></span>
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

<?= $this->render('../partials/_newOffers') ?>
