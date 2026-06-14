<?php

namespace tests\unit\models;

use app\models\EditOfferForm;
use app\models\NewOfferForm;
use app\models\RegisterForm;
use Codeception\Test\Unit;

class FormValidationTest extends Unit
{
    public function testNewOfferRequiresImage(): void
    {
        $form = new NewOfferForm([
            'title' => 'Достаточно длинный заголовок',
            'description' => 'Описание объявления, содержащее больше пятидесяти символов для проверки.',
            'price' => 100,
            'type' => 'sell',
            'category_id' => [1],
        ]);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('imageFiles', $form->errors);
    }

    public function testEditOfferAllowsExistingImage(): void
    {
        $form = new EditOfferForm([
            'title' => 'Достаточно длинный заголовок',
            'description' => 'Описание объявления, содержащее больше пятидесяти символов для проверки.',
            'price' => 100,
            'type' => 'sell',
            'category_id' => [1],
            'hasExistingImages' => true,
        ]);

        $this->assertTrue($form->validate());
    }

    public function testRegisterRejectsInvalidEmailAndPassword(): void
    {
        $form = new RegisterForm([
            'name' => 'Тестовый Пользователь',
            'email' => 'invalid-email',
            'password' => '123',
            'password_repeat' => '321',
        ]);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('email', $form->errors);
        $this->assertArrayHasKey('password', $form->errors);
        $this->assertArrayHasKey('password_repeat', $form->errors);
    }
}
