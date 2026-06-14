<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Авторизация';
?>

<section class="login">
    <h1 class="visually-hidden">Логин</h1>
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'login__form form', 'enctype' => 'multipart/form-data'],
    ]) ?>
    <div class="login__title">
        <?= Html::a('Регистрация', ['/register'], ['class' => 'login__link']) ?>
        <h2>Вход</h2>
    </div>
    <?= $form->field($loginForm, 'email', [
        'template' => '<div class="form__field login__field">{input}{label}{error}</div>',
        'inputOptions' => ['class' => 'js-field', 'id' => 'user-email'],
        'labelOptions' => ['for' => 'user-email'],
    ])->textInput()->label('Эл. почта') ?>

    <?= $form->field($loginForm, 'password', [
        'template' => '<div class="form__field login__field">{input}{label}{error}</div>',
        'inputOptions' => ['class' => 'js-field', 'id' => 'user-password'],
        'labelOptions' => ['for' => 'user-password'],
    ])->passwordInput(['value' => ''])->label('Пароль') ?>

    <?= Html::submitButton('Войти', ['class' => 'login__button btn btn--medium js-button']) ?>
    <?= Html::a('Войти через <span class="icon icon--vk"></span>', ['/login/vk'], [
        'class' => 'btn btn--small btn--flex btn--white',
    ]) ?>
    <?php ActiveForm::end(); ?>
</section>
