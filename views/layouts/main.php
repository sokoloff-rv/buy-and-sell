<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header class="header <?= Yii::$app->user->isGuest ? '' : 'header--logged' ?> ">
        <div class="header__wrapper">
            <a class="header__logo logo" href="/">
                <img src="/img/logo.svg" width="179" height="34" alt="Логотип Куплю Продам">
            </a>
            <nav class="header__user-menu">
                <ul class="header__list">
                    <li class="header__item <?= ($controller == 'my' && $action == 'index') ? 'header__item--active' : '' ?>">
                        <a href="<?= Url::to(['my/index']) ?>">Публикации</a>
                    </li>
                    <li class="header__item <?= ($controller == 'my' && $action == 'comments') ? 'header__item--active' : '' ?>">
                        <a href="<?= Url::to(['my/comments']) ?>">Комментарии</a>
                    </li>
                </ul>
            </nav>

            <?php
            $searchModel = Yii::$app->search->getSearchModel();
            $form = ActiveForm::begin([
                'action' => ['/search/index'],
                'method' => 'get',
                'options' => ['class' => 'search', 'autocomplete' => 'off'],
            ]);
            ?>
            <?= $form->field($searchModel, 'query', [
                'options' => ['class' => ''],
                'template' => '{input}<div class="search__icon"></div><div class="search__close-btn"></div>',
            ])->textInput(['placeholder' => 'Поиск', 'aria-label' => 'Поиск']) ?>
            <?php ActiveForm::end(); ?>


            <?php if (Yii::$app->user->isGuest) : ?>
                <a class="header__input" href="/register">Вход и регистрация</a>
            <?php else : ?>
                <a class="header__avatar avatar" href="#">
                    <img src="/<?= Yii::$app->user->identity->avatar ?>" alt="Аватар пользователя">
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main class="page-content">
        <?php if (!empty($this->params['breadcrumbs'])) : ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </main>

    <footer class="page-footer">
        <div class="page-footer__wrapper">
            <div class="page-footer__col">
                <a href="/" class="page-footer__logo-academy" aria-label="Ссылка на сайт HTML-Академии">
                    <svg width="132" height="46">
                        <use xlink:href="img/sprite_auto.svg#logo-htmlac"></use>
                    </svg>
                </a>
                <p class="page-footer__copyright">© 2019 Проект Академии</p>
            </div>
            <div class="page-footer__col">
                <a href="/" class="page-footer__logo logo">
                    <img src="/img/logo.svg" width="179" height="35" alt="Логотип Куплю Продам">
                </a>
            </div>
            <div class="page-footer__col">
                <ul class="page-footer__nav">
                    <li>
                        <a href="/">Вход и регистрация</a>
                    </li>
                    <li>
                        <a href="/">Создать объявление</a>
                    </li>
                </ul>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
