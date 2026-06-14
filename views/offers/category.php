<?php

use yii\helpers\Html;

$this->title = $category->name;
?>

<?= $this->render('../partials/_categories', ['categories' => $categories]) ?>
<section class="tickets-list">
    <h2 class="visually-hidden">Объявления категории</h2>
    <div class="tickets-list__wrapper">
        <div class="tickets-list__header">
            <p class="tickets-list__title"><?= Html::encode($category->name) ?> <span class="js-qty"><?= (int) $pagination->totalCount ?></span></p>
        </div>
        <?php if (!$offers) : ?>
            <p>Объявления отсутствуют</p>
        <?php else : ?>
            <ul>
                <?php foreach ($offers as $offer) : ?>
                    <li class="tickets-list__item">
                        <?= $this->render('../partials/_offerCard', ['offer' => $offer]) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php if ($pagination->pageCount > 1) : ?>
        <div class="tickets-list__pagination">
            <ul class="pagination">
                <?php for ($page = 0; $page < $pagination->pageCount; $page++) : ?>
                    <li class="<?= $page === $pagination->page ? 'active' : '' ?>">
                        <?php if ($page === $pagination->page) : ?>
                            <span><?= $page + 1 ?></span>
                        <?php else : ?>
                            <a href="<?= Html::encode($pagination->createUrl($page)) ?>"><?= $page + 1 ?></a>
                        <?php endif; ?>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>
    <?php endif; ?>
</section>
