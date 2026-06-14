<?php

use yii\helpers\Html;
use app\models\Image;

$viewUrl = ['/offers/index', 'id' => $offer->id];
$offerUrl = !empty($showActions) ? ['/offers/edit', 'id' => $offer->id] : $viewUrl;

$previewRetina = Image::retinaUrl($offer->previewImage);
$previewOptions = ['alt' => 'Изображение товара'];
if ($previewRetina !== '') {
    $previewOptions['srcset'] = $previewRetina . ' 2x';
}
?>

<div class="ticket-card">
    <div class="ticket-card__img">
        <?= Html::a(Html::img($offer->previewImage, $previewOptions), $offerUrl) ?>
    </div>
    <div class="ticket-card__info">
        <span class="ticket-card__label"><?= Html::encode($offer->label) ?></span>
        <div class="ticket-card__categories">
            <?php foreach ($offer->categories as $category) : ?>
                <?= Html::a(Html::encode($category->name), ['offers/category', 'id' => $category->id]) ?>
            <?php endforeach; ?>
        </div>
        <div class="ticket-card__header">
            <h3 class="ticket-card__title">
                <?= Html::a(Html::encode($offer->title), $offerUrl) ?>
            </h3>
            <p class="ticket-card__price">
                <span class="js-sum"><?= Html::encode(round($offer->price, 0)) ?></span> ₽
            </p>
        </div>
        <div class="ticket-card__desc">
            <p><?= Html::encode($offer->announcement) ?></p>
        </div>
    </div>
    <?php if (!empty($showActions)): ?>
        <?php
        $viewIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
        $editIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z"/><path d="m14 7 3 3"/></svg>';
        $deleteIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5"/></svg>';
        ?>
        <div class="ticket-card__actions">
            <?= Html::a($viewIcon . Html::tag('span', 'Просмотр', ['class' => 'visually-hidden']), $viewUrl, [
                'class' => 'ticket-card__action ticket-card__action--view',
                'aria-label' => 'Просмотр',
                'title' => 'Просмотр',
            ]) ?>
            <?= Html::a($editIcon . Html::tag('span', 'Редактирование', ['class' => 'visually-hidden']), ['/offers/edit', 'id' => $offer->id], [
                'class' => 'ticket-card__action ticket-card__action--edit',
                'aria-label' => 'Редактирование',
                'title' => 'Редактирование',
            ]) ?>
            <?= Html::beginForm(['/offers/delete', 'id' => $offer->id], 'post', ['class' => 'ticket-card__action-form']) ?>
            <?= Html::submitButton($deleteIcon . Html::tag('span', 'Удалить', ['class' => 'visually-hidden']), [
                'class' => 'ticket-card__action ticket-card__action--delete js-delete',
                'aria-label' => 'Удалить',
                'title' => 'Удалить',
            ]) ?>
            <?= Html::endForm() ?>
        </div>
    <?php endif; ?>
</div>
