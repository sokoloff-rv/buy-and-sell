<?php

/* @var $this yii\web\View */

$this->title = 'Комментарии';
?>

<section class="comments">
    <div class="comments__wrapper">
        <h1 class="visually-hidden">Страница комментариев</h1>
        <?php foreach ($offersWithComments as $data) : ?>
            <div class="comments__block">
                <div class="comments__header">
                    <a href="#" class="announce-card">
                        <h2 class="announce-card__title">
                            <?= $data['offer']->title ?>
                        </h2>
                        <span class="announce-card__info">
                            <span class="announce-card__price">₽ 
                                <?= round($data['offer']->price, 0) ?>
                            </span>
                            <span class="announce-card__type"><?= $data['offer']->label ?></span>
                        </span>
                    </a>
                </div>
                <ul class="comments-list">
                    <?php foreach ($data['comments'] as $comment) : ?>
                        <li class="js-card">
                            <div class="comment-card">
                                <div class="comment-card__header">
                                    <a href="#" class="comment-card__avatar avatar">
                                        <img src="/<?= $comment->user->avatar ?>" alt="Аватар пользователя">
                                    </a>
                                    <p class="comment-card__author">
                                        <?= $comment->user->name ?>
                                    </p>
                                </div>
                                <div class="comment-card__content">
                                    <p>
                                        <?= $comment->text ?>
                                    </p>
                                </div>
                                <button class="comment-card__delete js-delete" type="button" onclick="location.href='<?= Yii::$app->urlManager->createUrl(['my/delete-comment', 'id' => $comment->id]) ?>'">Удалить</button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</section>
