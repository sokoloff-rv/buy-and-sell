<?php

use app\models\User;
use Double\FakeFirebaseStore;

class ChatCest
{
    public function _before(FunctionalTester $I): void
    {
        FakeFirebaseStore::reset();
    }

    public function authenticatedUserReceivesCustomToken(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('chat/token');
        $I->seeResponseCodeIsSuccessful();
        $I->see('fake-custom-token-1');
    }

    public function guestIsRedirectedFromToken(FunctionalTester $I): void
    {
        $I->amOnRoute('chat/token');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Вход');
    }

    public function buyerOpensDialogAndServerCreatesMeta(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/index', ['id' => 2]);
        $csrf = $I->grabValueFrom('input[name="_csrf"]');

        $I->sendAjaxPostRequest('/chat/open', ['offerId' => 2, '_csrf' => $csrf]);
        $I->seeResponseCodeIsSuccessful();

        $meta = FakeFirebaseStore::get('chats/2/1/meta');
        $I->assertIsArray($meta);
        $I->assertSame('2', $meta['sellerId']);
        $I->assertSame('1', $meta['buyerId']);
        $I->assertSame('2', $meta['offerId']);

        $I->sendAjaxPostRequest('/chat/open', ['offerId' => 2, '_csrf' => $csrf]);
        $I->seeResponseCodeIsSuccessful();
        $this->assertSingleMeta($I);
    }

    public function ownerSeesOnlyValidDialogs(FunctionalTester $I): void
    {
        FakeFirebaseStore::set('chats/2/1/meta', [
            'sellerId' => '2',
            'buyerId' => '1',
            'offerId' => '2',
            'updatedAt' => 1000,
        ]);
        FakeFirebaseStore::set('chats/2/2/meta', [
            'sellerId' => '999',
            'buyerId' => '2',
            'offerId' => '2',
            'updatedAt' => 1000,
        ]);

        $I->amLoggedInAs(User::findOne(2));
        $I->amOnRoute('chat/dialogs', ['offerId' => 2]);
        $I->seeResponseCodeIsSuccessful();

        $data = json_decode($I->grabPageSource(), true);
        $I->assertCount(1, $data['dialogs']);
        $I->assertSame('1', $data['dialogs'][0]['buyerId']);
    }

    public function nonOwnerCannotSeeDialogs(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('chat/dialogs', ['offerId' => 2]);
        $I->seeResponseCodeIs(403);
    }

    public function dialogsForMissingOfferReturns404(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('chat/dialogs', ['offerId' => 999]);
        $I->seeResponseCodeIs(404);
    }

    private function assertSingleMeta(FunctionalTester $I): void
    {
        $offerNode = FakeFirebaseStore::get('chats/2');
        $I->assertIsArray($offerNode);
        $I->assertCount(1, $offerNode);
    }
}
