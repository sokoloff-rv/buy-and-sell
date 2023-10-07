<?php

use app\models\Offer;

$offers = Offer::find()->orderBy(['created_at' => SORT_DESC])->limit(8)->all();
?>

<section class="tickets-list">
    <h2 class="visually-hidden">Самые новые предложения</h2>
    <div class="tickets-list__wrapper">
        <div class="tickets-list__header">
            <p class="tickets-list__title">Самое свежее</p>
        </div>
        <ul>
            <?php foreach ($offers as $offer) : ?>
                <li class="tickets-list__item">
                    <?= $this->render('_offerCard', ['offer' => $offer]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
