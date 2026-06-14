<?php

$this->title = 'Главная страница';
?>

<?php if (!$newOffers) : ?>
    <section class="tickets-list">
        <div class="tickets-list__wrapper">
            <p>На сайте еще не опубликовано ни одного объявления.</p>
            <a class="btn btn--medium" href="/login">Вход и регистрация</a>
        </div>
    </section>
<?php else : ?>
    <?= $this->render('../partials/_categories', ['categories' => $categories]) ?>
    <?= $this->render('../partials/_newOffers', ['offers' => $newOffers]) ?>
    <?= $this->render('../partials/_mostDiscussed', ['offers' => $mostDiscussed]) ?>
<?php endif; ?>
