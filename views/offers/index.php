<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Image;

$this->title = $offer->title;
?>

<section class="ticket">
    <div class="ticket__wrapper">
        <h1 class="visually-hidden">Карточка объявления</h1>
        <div class="ticket__content">
            <?php foreach ($offer->images as $image) : ?>
                <?php
                    $imageRetina = Image::retinaUrl($image->image_path);
                    $imageOptions = ['alt' => 'Изображение товара'];
                    if ($imageRetina !== '') {
                        $imageOptions['srcset'] = $imageRetina . ' 2x';
                    }
                ?>
                <div class="ticket__img">
                    <?= Html::img($image->image_path, $imageOptions) ?>
                </div>
            <?php endforeach; ?>
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
                        <span><?= Yii::$app->formatter->asDate($offer->created_at, 'dd MMMM yyyy') ?></span>
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
                            <a href="<?= Url::to(['offers/category', 'id' => $category->id]) ?>" class="category-tile category-tile--small">
                                <span class="category-tile__image">
                                    <img src="<?= Html::encode($category->imageUrl) ?>" srcset="<?= Html::encode($category->retinaImageUrl) ?> 2x" alt="Иконка категории">
                                </span>
                                <span class="category-tile__label"><?= Html::encode($category->name) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->can('deleteOffer')) : ?>
                <?= Html::beginForm(['/offers/delete', 'id' => $offer->id], 'post') ?>
                <?= Html::submitButton('Удалить объявление', ['class' => 'btn btn--small']) ?>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </div>
        <div class="ticket__comments">
            <h2 class="ticket__subtitle">Комментарии</h2>
            <?php if (Yii::$app->user->isGuest) : ?>
                <div class="ticket__comment-form">
                    <p>Отправка комментариев доступна только для зарегистрированных пользователей.</p>
                    <?= Html::a('Вход и регистрация', ['/login']) ?>
                </div>
            <?php else : ?>
                <div class="ticket__comment-form">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'form comment-form']]); ?>
                <div class="comment-form__header">
                    <a href="#" class="comment-form__avatar avatar">
                        <img src="<?= Html::encode(Yii::$app->user->identity->avatarUrl) ?>" alt="Аватар пользователя">
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
            <?php endif; ?>
            <div class="ticket__comments-list">
                <ul class="comments-list">
                    <?php foreach ($comments as $comment) : ?>
                        <li>
                            <div class="comment-card">
                                <div class="comment-card__header">
                                    <a href="#" class="comment-card__avatar avatar">
                                        <img src="<?= Html::encode($comment->user->avatarUrl) ?>" alt="Аватар пользователя">
                                    </a>
                                    <p class="comment-card__author">
                                        <?= Html::encode($comment->user->name) ?>
                                    </p>
                                </div>
                                <div class="comment-card__content">
                                    <p><?= Html::encode($comment->text) ?></p>
                                </div>
                                <?php if (!Yii::$app->user->isGuest && Yii::$app->user->can('deleteComment')) : ?>
                                    <?= Html::beginForm(['/my/delete-comment', 'id' => $comment->id], 'post') ?>
                                    <?= Html::submitButton('Удалить', ['class' => 'comment-card__delete js-delete']) ?>
                                    <?= Html::endForm() ?>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
