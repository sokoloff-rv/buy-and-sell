<?php

use app\models\Offer;

$offers = Offer::getMostDiscussed();

if (count($offers)) :
?>

<section class="tickets-list">
    <h2 class="visually-hidden">Самые обсуждаемые предложения</h2>
    <div class="tickets-list__wrapper">
        <div class="tickets-list__header">
            <p class="tickets-list__title">Самые обсуждаемые</p>
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

<?php endif; ?>
