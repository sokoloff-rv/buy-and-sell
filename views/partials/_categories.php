<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Category;

$categories = Category::find()->all();
$currentCategoryId = Yii::$app->request->get('id');

?>

<section class="categories-list">
    <ul class="categories-list__wrapper">
        <?php foreach ($categories as $category) : ?>
            <li class="categories-list__item">
                <?php
                $isActive = $category->id == $currentCategoryId;
                $tileClass = $isActive ? 'category-tile--active' : 'category-tile--default';
                ?>
                <a href="<?= Url::to(['offers/category', 'id' => $category->id]) ?>" class="category-tile <?= $tileClass ?>">
                    <span class="category-tile__image">
                        <?= Html::img($category->image, ['srcset' => $category->image . ' 2x', 'alt' => 'Иконка категории']) ?>
                    </span>
                    <span class="category-tile__label">
                        <?= Html::encode($category->name) ?>
                        <span class="category-tile__qty js-qty"><?= count($category->offers) ?></span>
                    </span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
