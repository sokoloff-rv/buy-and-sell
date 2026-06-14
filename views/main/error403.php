<?php

use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="ru" class="html-not-found">
<head>
    <title>Ошибка 403</title>
    <?php $this->head() ?>
</head>
<body class="body-not-found">
<?php $this->beginBody() ?>
<main>
    <section class="error">
        <h1 class="error__title">403</h1>
        <h2 class="error__subtitle">Доступ запрещен</h2>
        <ul class="error__list">
            <li class="error__item"><?= Html::a('Главная страница', ['/main/index']) ?></li>
            <li class="error__item"><?= Html::a('Вход и регистрация', ['/login']) ?></li>
        </ul>
    </section>
</main>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
