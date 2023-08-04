<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = $offer->title;
?>

<section class="ticket">
    <div class="ticket__wrapper">
        <h1 class="visually-hidden">Карточка объявления</h1>
        <div class="ticket__content">
            <div class="ticket__img">
                <img src="<?= Html::encode($offer->images[0]->image_path) ?>" alt="Изображение товара">
            </div>
            <div class="ticket__info">
                <h2 class="ticket__title"><?= Html::encode($offer->title) ?></h2>
                <div class="ticket__header">
                    <p class="ticket__price"><span class="js-sum"><?= Html::encode((round($offer->price, 0))) ?></span> ₽</p>
                    <p class="ticket__action"><?= Html::encode($offer->label) ?></p>
                </div>
                <div class="ticket__desc">
                    <p><?= Html::encode($offer->description) ?></p>
                </div>
                <div class="ticket__data">
                    <p>
                        <b>Дата добавления:</b>
                        <span><?= Yii::$app->formatter->asDate($offer->created_at, 'long') ?></span>
                    </p>
                    <p>
                        <b>Автор:</b>
                        <?= Html::encode($offer->user->name) ?>
                    </p>
                    <p>
                        <b>Контакты:</b>
                        <a href="mailto:<?= Html::encode($offer->user->email) ?>"><?= Html::encode($offer->user->email) ?></a>
                    </p>
                </div>
                <ul class="ticket__tags">
                    <?php foreach ($offer->categories as $category) : ?>
                        <li>
                            <a href="<?= Url::to(['category/view', 'id' => $category->id]) ?>" class="category-tile category-tile--small">
                                <span class="category-tile__image">
                                    <img src="<?= $category->image ?>" alt="Иконка категории">
                                </span>
                                <span class="category-tile__label"><?= Html::encode($category->name) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="ticket__comments">
            <h2 class="ticket__subtitle">Коментарии</h2>
            <div class="ticket__comment-form">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'form comment-form']]); ?>
                <div class="comment-form__header">
                    <a href="#" class="comment-form__avatar avatar">
                        <img src="/<?= Yii::$app->user->identity->avatar ?>" alt="Аватар пользователя">
                    </a>
                    <p class="comment-form__author">Вам слово</p>
                </div>
                <div class="comment-form__field">
                    <div class="form__field">
                        <?= $form->field($newCommentForm, 'text', ['template' => "{input}\n{label}\n{hint}\n{error}"])
                            ->textarea(['id' => 'comment-field', 'cols' => 30, 'rows' => 10, 'class' => 'js-field'])
                            ->label('Текст комментария') ?>
                        <span>Обязательное поле</span>
                    </div>
                </div>
                <?= Html::submitButton('Отправить', ['class' => 'comment-form__button btn btn--white js-button', 'disabled' => true]) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

    </div>
</section>
