<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $registerForm app\models\RegisterForm */
/* @var $form ActiveForm */

$this->title = 'Регистрация';
?>

<section class="sign-up">
    <h1 class="visually-hidden">Регистрация</h1>
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'sign-up__form form', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
    ]) ?>
        <div class="sign-up__title">
            <h2>Регистрация</h2>
            <?= Html::a('Вход', ['/login'], ['class' => 'sign-up__link']) ?>
        </div>
        <div class="sign-up__avatar-container js-preview-container">
            <div class="sign-up__avatar js-preview"></div>
            <div class="sign-up__field-avatar">
                <?= $form->field($registerForm, 'avatar')->fileInput(['class' => 'visually-hidden js-file-field', 'id' => 'avatar'])->label(false) ?>
                <label for="avatar">
                    <span class="sign-up__text-upload">Загрузить аватар…</span>
                    <span class="sign-up__text-another">Загрузить другой аватар…</span>
                </label>
            </div>
        </div>
        <?= $form->field($registerForm, 'name', [
            'template' => '<div class="form__field sign-up__field">{input}{label}{error}</div>',
            'inputOptions' => ['class' => 'js-field', 'id' => 'user-name'],
            'labelOptions' => ['for' => 'user-name'],
        ])->textInput()->label('Имя и фамилия') ?>
        <?= $form->field($registerForm, 'email', [
            'template' => '<div class="form__field sign-up__field">{input}{label}{error}</div>',
            'inputOptions' => ['class' => 'js-field', 'id' => 'user-email'],
            'labelOptions' => ['for' => 'user-email'],
        ])->textInput()->label('Эл. почта') ?>
        <?= $form->field($registerForm, 'password', [
            'template' => '<div class="form__field sign-up__field">{input}{label}{error}</div>',
            'inputOptions' => ['class' => 'js-field', 'id' => 'user-password'],
            'labelOptions' => ['for' => 'user-password'],
        ])->passwordInput()->label('Пароль') ?>
        <?= $form->field($registerForm, 'password_repeat', [
            'template' => '<div class="form__field sign-up__field">{input}{label}{error}</div>',
            'inputOptions' => ['class' => 'js-field', 'id' => 'user-password-again'],
            'labelOptions' => ['for' => 'user-password-again'],
        ])->passwordInput()->label('Пароль еще раз') ?>
        <?= Html::submitButton('Создать аккаунт', ['class' => 'sign-up__button btn btn--medium js-button']) ?>
        <a class="btn btn--small btn--flex btn--white" href="/login/vk?authclient=vkontakte">
            Войти через
            <span class="icon icon--vk"></span>
        </a>
    <?php ActiveForm::end(); ?>
</section>
