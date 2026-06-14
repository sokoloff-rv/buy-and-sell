<?php

use app\models\Offer;
use app\models\User;
use app\models\Comment;

class ProjectCest
{
    public function missingOfferReturns404(FunctionalTester $I): void
    {
        $I->amOnRoute('offers/index', ['id' => 999]);
        $I->seeResponseCodeIs(404);
    }

    public function guestIsRedirectedFromPrivatePage(FunctionalTester $I): void
    {
        $I->amOnRoute('offers/add');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Вход');
    }

    public function userCannotEditForeignOffer(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/edit', ['id' => 2]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteByGetIsRejected(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/delete', ['id' => 1]);
        $I->seeResponseCodeIs(405);

        $I->amOnRoute('my/delete-comment', ['id' => 1]);
        $I->seeResponseCodeIs(405);
    }

    public function deleteWithoutCsrfIsRejected(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->sendAjaxPostRequest('/offers/delete?id=1');
        $I->seeResponseCodeIs(400);
        $I->seeRecord(Offer::class, ['id' => 1]);
    }

    public function userHtmlIsEscaped(FunctionalTester $I): void
    {
        $offer = Offer::findOne(1);
        $offer->title = '<script>alert(1)</script>';
        $offer->save();

        $I->amOnRoute('offers/index', ['id' => 1]);
        $I->seeInSource('&lt;script&gt;alert(1)&lt;/script&gt;');
        $I->dontSeeInSource('<script>alert(1)</script>');
    }

    public function ownerCanEditOfferWithoutNewImage(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/edit', ['id' => 1]);
        $I->submitForm('.ticket-form__form', [
            'EditOfferForm[title]' => 'Обновленное тестовое объявление',
            'EditOfferForm[description]' => 'Обновленное подробное описание объявления длиной более пятидесяти символов.',
            'EditOfferForm[category_id]' => [1],
            'EditOfferForm[price]' => 1700,
            'EditOfferForm[type]' => 'sell',
        ], 'Сохранить');
        $I->seeRecord(Offer::class, ['id' => 1, 'title' => 'Обновленное тестовое объявление']);
    }

    public function userCanCreateComment(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/index', ['id' => 2]);
        $I->submitForm('.comment-form', [
            'NewCommentForm[text]' => 'Новый функциональный тестовый комментарий.',
        ], 'Отправить');
        $I->seeRecord(Comment::class, [
            'offer_id' => 2,
            'user_id' => 1,
            'text' => 'Новый функциональный тестовый комментарий.',
        ]);
    }

    public function ownerCanDeleteOfferThroughPostForm(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('my/index');
        $I->click('Удалить');
        $I->dontSeeRecord(Offer::class, ['id' => 1]);
    }

    public function userCanLogOutThroughAvatarMenu(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('main/index');
        $I->submitForm('.user-popup form', []);

        $I->amOnRoute('offers/add');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Вход');
    }

    public function vkRegistrationCanBeCompletedWithEmail(FunctionalTester $I): void
    {
        Yii::$app->session->set('vkRegistration', [
            'user_id' => 777777,
            'first_name' => 'Новый',
            'last_name' => 'Пользователь',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $I->amOnRoute('login/vk-email');
        $I->see('Укажите эл. почту');
        $I->submitForm('.login__form', [
            'VkEmailForm[email]' => 'new-vk-user@example.com',
        ], 'Завершить регистрацию');

        $I->seeRecord(User::class, [
            'email' => 'new-vk-user@example.com',
            'vk_id' => 777777,
            'password' => null,
        ]);
        $user = User::findOne(['vk_id' => 777777]);
        $I->assertNotNull(Yii::$app->authManager->getAssignment('user', (string) $user->id));
        $I->assertNull(Yii::$app->session->get('vkRegistration'));
    }

    public function vkRegistrationDoesNotLinkEnteredExistingEmail(FunctionalTester $I): void
    {
        Yii::$app->session->set('vkRegistration', [
            'user_id' => 888888,
            'first_name' => 'Другой',
            'last_name' => 'Пользователь',
        ]);

        $I->amOnRoute('login/vk-email');
        $I->submitForm('.login__form', [
            'VkEmailForm[email]' => 'user@example.com',
        ], 'Завершить регистрацию');

        $I->see('Этот email уже используется.');
        $I->dontSeeRecord(User::class, ['vk_id' => 888888]);
    }
}
