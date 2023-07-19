<?php

use app\assets\AppAsset;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$this->beginPage();
?>

<!DOCTYPE html>
<html lang="ru" class="html-not-found">

<head>
    <title>Ошибка 404</title>
    <?php $this->head() ?>
</head>

<body class="body-not-found">
    <?php $this->beginBody() ?>

    <main>
        <section class="error">
            <h1 class="error__title">404</h1>
            <h2 class="error__subtitle">Страница не найдена</h2>
            <ul class="error__list">
                <li class="error__item">
                    <a href="/login">Вход и регистрация</a>
                </li>
                <li class="error__item">
                    <a href="/offers/add">Новая публикация</a>
                </li>
                <li class="error__item">
                    <a href="/">Главная страница</a>
                </li>
            </ul>
            <form class="error__search search search--small" method="get" action="#" autocomplete="off">
                <input type="search" name="query" placeholder="Поиск" aria-label="Поиск">
                <div class="search__icon"></div>
                <div class="search__close-btn"></div>
            </form>
            <a class="error__logo logo" href="main.html">
                <img src="/img/logo.svg" width="179" height="34" alt="Логотип Куплю Продам">
            </a>
        </section>
    </main>

    <script src="js/vendor.js"></script>
    <script src="js/main.js"></script>

    <?php $this->endBody() ?>
</body>

</html>

<?php $this->endPage() ?>
