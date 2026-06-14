<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Завершение регистрации';
?>

<section class="login">
    <h1 class="visually-hidden">Завершение регистрации через ВК</h1>
    <?php $activeForm = ActiveForm::begin([
        'options' => ['class' => 'login__form form', 'autocomplete' => 'off'],
    ]) ?>
    <div class="login__title">
        <h2>Укажите эл. почту</h2>
    </div>
    <p class="login__hint">Для завершения регистрации укажите свой email.</p>
    <?= $activeForm->field($form, 'email', [
        'template' => '<div class="form__field login__field">{input}{label}{error}</div>',
        'inputOptions' => ['class' => 'js-field', 'id' => 'vk-email'],
        'labelOptions' => ['for' => 'vk-email'],
    ])->textInput()->label('Эл. почта') ?>
    <?= Html::submitButton('Завершить регистрацию', ['class' => 'login__button login__button--wide btn btn--medium js-button']) ?>
    <?php ActiveForm::end(); ?>
</section>
