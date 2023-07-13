<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RegisterForm */
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
                <?= $form->field($model, 'avatar')->fileInput(['class' => 'visually-hidden js-file-field', 'id' => 'avatar'])->label(false) ?>
                <label for="avatar">
                    <span class="sign-up__text-upload">Загрузить аватар…</span>
                    <span class="sign-up__text-another">Загрузить другой аватар…</span>
                </label>
            </div>
        </div>
        <?= $form->field($model, 'name')->textInput(['class' => 'js-field', 'id' => 'user-name'])->label('Имя и фамилия') ?>
        <?= $form->field($model, 'email')->textInput(['class' => 'js-field', 'id' => 'user-email'])->label('Эл. почта') ?>
        <?= $form->field($model, 'password')->passwordInput(['class' => 'js-field', 'id' => 'user-password'])->label('Пароль') ?>
        <?= $form->field($model, 'password_repeat')->passwordInput(['class' => 'js-field', 'id' => 'user-password-again'])->label('Пароль еще раз') ?>
        <div class="form-group">
            <?= Html::submitButton('Создать аккаунт', ['class' => 'sign-up__button btn btn--medium js-button']) ?>
        </div>
        <a class="btn btn--small btn--flex btn--white" href="#">
            Войти через
            <span class="icon icon--vk"></span>
        </a>
    <?php ActiveForm::end(); ?>
</section>
