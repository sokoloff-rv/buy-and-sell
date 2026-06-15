<?php

use app\models\User;
use yii\base\Event;
use yii\db\ActiveRecord;

class AuthCest
{
    public function _after(FunctionalTester $I): void
    {
        $dir = Yii::getAlias('@runtime/test-uploads');
        foreach (glob($dir . '/*') ?: [] as $file) {
            @unlink($file);
        }
    }

    public function loginWithValidCredentialsSucceeds(FunctionalTester $I): void
    {
        $I->amOnRoute('login/index');
        $I->submitForm('.login__form', [
            'LoginForm[email]' => 'user@example.com',
            'LoginForm[password]' => 'password123',
        ], 'Войти');

        $I->amOnRoute('offers/add');
        $I->see('Новое объявление');
    }

    public function loginWithWrongPasswordShowsError(FunctionalTester $I): void
    {
        $I->amOnRoute('login/index');
        $I->submitForm('.login__form', [
            'LoginForm[email]' => 'user@example.com',
            'LoginForm[password]' => 'wrong-password',
        ], 'Войти');

        $I->see('Введен неверный пароль.');

        $I->amOnRoute('offers/add');
        $I->see('Вход');
    }

    public function vkUserCannotLoginWithPassword(FunctionalTester $I): void
    {
        $vkUser = new User([
            'name' => 'ВК Пользователь',
            'email' => 'vkuser@example.com',
            'vk_id' => 999001,
        ]);
        $vkUser->save(false);

        $I->amOnRoute('login/index');
        $I->submitForm('.login__form', [
            'LoginForm[email]' => 'vkuser@example.com',
            'LoginForm[password]' => 'any-password',
        ], 'Войти');

        $I->see('Введен неверный пароль.');
    }

    public function registrationCreatesUserWithHashedPasswordAndAvatar(FunctionalTester $I): void
    {
        $I->amOnRoute('register/index');
        $I->attachFile('#avatar', 'test-image.png');
        $I->submitForm('.sign-up__form', [
            'RegisterForm[name]' => 'Новый Пользователь',
            'RegisterForm[email]' => 'newcomer@example.com',
            'RegisterForm[password]' => 'secret123',
            'RegisterForm[password_repeat]' => 'secret123',
        ]);

        $user = User::findOne(['email' => 'newcomer@example.com']);
        $I->assertNotNull($user);
        $I->assertSame('Новый Пользователь', $user->name);
        $I->assertNotSame('secret123', $user->password);
        $I->assertTrue(Yii::$app->security->validatePassword('secret123', $user->password));
        $I->assertNotNull(Yii::$app->authManager->getAssignment('user', (string) $user->id));
        $I->assertStringStartsWith('/test-uploads/', $user->avatar);
        $I->assertFileExists(Yii::getAlias('@runtime/test-uploads') . '/' . basename($user->avatar));
    }

    public function registrationRollsBackUserAndAvatarWhenSaveFails(FunctionalTester $I): void
    {
        Event::on(User::class, ActiveRecord::EVENT_BEFORE_INSERT, [$this, '_cancelEvent']);
        try {
            $I->amOnRoute('register/index');
            $I->attachFile('#avatar', 'test-image.png');
            $I->submitForm('.sign-up__form', [
                'RegisterForm[name]' => 'Неуспешный Пользователь',
                'RegisterForm[email]' => 'failed-registration@example.com',
                'RegisterForm[password]' => 'secret123',
                'RegisterForm[password_repeat]' => 'secret123',
            ]);
        } finally {
            Event::off(User::class, ActiveRecord::EVENT_BEFORE_INSERT, [$this, '_cancelEvent']);
        }

        $I->dontSeeRecord(User::class, ['email' => 'failed-registration@example.com']);
        $I->assertSame([], glob(Yii::getAlias('@runtime/test-uploads') . '/*'));
        $I->see('Не удалось создать аккаунт.');
    }

    public function _cancelEvent($event): void
    {
        $event->isValid = false;
    }
}
