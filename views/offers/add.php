<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;
use yii\helpers\ArrayHelper;

$this->title = 'Новая публикация';
?>

<section class="ticket-form">
    <div class="ticket-form__wrapper">
        <h1 class="ticket-form__title"><?= Html::encode($this->title) ?></h1>
        <div class="ticket-form__tile">

            <?php $form = ActiveForm::begin(['options' => ['class' => 'ticket-form__form form', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off']]); ?>

            <div class="ticket-form__avatar-container js-preview-container">
                <div class="ticket-form__avatar js-preview"></div>
                <div class="ticket-form__field-avatar">
                    <?= $form->field($model, 'imageFiles[]', ['template' => "{input}\n{label}\n{hint}\n{error}"])
                        ->fileInput(['id' => 'avatar', 'class' => 'visually-hidden js-file-field'])
                        ->label('<span class="ticket-form__text-upload">Загрузить фото…</span><span class="ticket-form__text-another">Загрузить другое фото…</span>') ?>
                </div>
            </div>

            <div class="ticket-form__content">
                <div class="ticket-form__row">
                    <div class="form__field">
                        <?= $form->field($model, 'title', ['template' => "{input}\n{label}\n{hint}\n{error}"])
                            ->textInput(['id' => 'ticket-name', 'class' => 'js-field', 'required' => true])
                            ->label('Название') ?>
                        <span>Обязательное поле</span>
                    </div>
                </div>

                <div class="ticket-form__row">
                    <div class="form__field">
                        <?= $form->field($model, 'description', ['template' => "{input}\n{label}\n{hint}\n{error}"])
                            ->textarea(['id' => 'comment-field', 'class' => 'js-field', 'cols' => 30, 'rows' => 10])
                            ->label('Описание') ?>
                        <span>Обязательное поле</span>
                    </div>
                </div>

                <div class="ticket-form__row">
                    <?= $form->field($model, 'category_id', [
                        'template' => "{input}\n{hint}\n{error}"
                    ])->dropDownList(
                        ArrayHelper::map(Category::find()->all(), 'id', 'name'),
                        [
                            'prompt' => 'Выбрать категорию публикации',
                            'class' => 'form__select js-multiple-select',
                            'data-label' => 'Выбрать категорию публикации',
                            'id' => 'category-field'
                        ]
                    ) ?>
                </div>

                <div class="ticket-form__row">
                    <div class="form__field form__field--price">
                        <?= $form->field($model, 'price', ['template' => "{input}\n{label}\n{hint}\n{error}"])
                            ->input('number', ['id' => 'price-field', 'class' => 'js-field js-price', 'min' => 1, 'required' => true])
                            ->label('Цена') ?>
                        <span>Обязательное поле</span>
                    </div>

                    <?= $form->field($model, 'type', [
                        'template' => "{input}\n{hint}\n{error}"
                    ])->radioList(
                        ['buy' => 'Куплю', 'sell' => 'Продам'],
                        [
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $check = $checked ? ' checked="checked"' : '';
                                $return = '<div class="switch__item">';
                                $return .= '<input type="radio" id="' . $value . '-field" name="' . $name . '" value="' . $value . '" class="visually-hidden"' . $check . '>';
                                $return .= '<label for="' . $value . '-field" class="switch__button">' . ucwords($label) . '</label>';
                                $return .= '</div>';
                                return $return;
                            },
                            'tag' => 'div',
                            'class' => 'form__switch switch'
                        ]
                    ) ?>

                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Опубликовать', ['class' => 'form__button btn btn--medium js-button', 'disabled' => true]) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</section>
