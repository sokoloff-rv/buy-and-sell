<?php

use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $offers app\models\Offer[] */
/* @var $pagination yii\data\Pagination */

$this->title = 'Категория';
?>

<?= $this->render('../partials/_categories') ?>
<section class="tickets-list">
    <h2 class="visually-hidden">Объявления категории</h2>
    <div class="tickets-list__wrapper">
        <div class="tickets-list__header">
            <p class="tickets-list__title">Категория</p>
        </div>
        <ul>
            <?php foreach ($offers as $offer) : ?>
                <?= $this->render('../partials/_offerCard', ['offer' => $offer]) ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="tickets-list__pagination">
        <?= LinkPager::widget([
            'pagination' => $pagination,
            'activePageCssClass' => 'active',
            'nextPageLabel' => 'дальше',
            'options' => ['class' => 'pagination'],
            'linkOptions' => ['class' => ''],
        ]) ?>
    </div>
</section>
