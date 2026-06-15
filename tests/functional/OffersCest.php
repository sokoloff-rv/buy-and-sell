<?php

use app\models\Image;
use app\models\Offer;
use app\models\User;

class OffersCest
{
    public function _after(FunctionalTester $I): void
    {
        $dir = Yii::getAlias('@runtime/test-uploads');
        foreach (glob($dir . '/*') ?: [] as $file) {
            @unlink($file);
        }
    }

    public function userCanCreateOfferWithImage(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/add');

        $I->attachFile('#avatar', 'test-image.png');
        $I->submitForm('.ticket-form__form', [
            'NewOfferForm[title]' => 'Свежее объявление с фото',
            'NewOfferForm[description]' => 'Подробное описание объявления длиной заметно больше пятидесяти символов.',
            'NewOfferForm[category_id]' => ['1'],
            'NewOfferForm[price]' => '650',
            'NewOfferForm[type]' => 'sell',
        ]);

        $offer = Offer::findOne(['title' => 'Свежее объявление с фото']);
        $I->assertNotNull($offer);
        $I->assertSame(1, (int) $offer->user_id);
        $I->assertCount(1, $offer->categories);
        $I->assertCount(1, $offer->images);
        $I->assertFileExists(Yii::getAlias('@runtime/test-uploads') . '/' . basename($offer->images[0]->image_path));
    }

    public function ownerCanReplaceOfferImage(FunctionalTester $I): void
    {
        if (!is_dir(Yii::getAlias('@runtime/test-uploads'))) {
            mkdir(Yii::getAlias('@runtime/test-uploads'), 0775, true);
        }
        $oldPath = Yii::getAlias('@runtime/test-uploads/old-offer-image.png');
        copy(codecept_data_dir('test-image.png'), $oldPath);
        Image::updateAll(['image_path' => '/test-uploads/old-offer-image.png'], ['offer_id' => 1]);

        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/edit', ['id' => 1]);

        $I->attachFile('#avatar', 'test-image.png');
        $I->submitForm('.ticket-form__form', [
            'EditOfferForm[title]' => 'Объявление с новым фото',
            'EditOfferForm[description]' => 'Обновлённое описание объявления длиной заметно больше пятидесяти символов.',
            'EditOfferForm[category_id]' => ['1'],
            'EditOfferForm[price]' => '1800',
            'EditOfferForm[type]' => 'sell',
        ]);

        $offer = Offer::findOne(1);
        $I->assertSame('Объявление с новым фото', $offer->title);
        $I->assertCount(1, $offer->images);
        $newPath = $offer->images[0]->image_path;
        $I->assertStringStartsWith('/test-uploads/', $newPath);
        $I->assertFileExists(Yii::getAlias('@runtime/test-uploads') . '/' . basename($newPath));
        $I->assertFileDoesNotExist($oldPath);
    }

    public function moderatorCanDeleteForeignOffer(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(2));
        $I->amOnRoute('offers/index', ['id' => 1]);
        $I->click('Удалить объявление');

        $I->dontSeeRecord(Offer::class, ['id' => 1]);
    }

    public function deletingOfferRemovesRelatedImageRecords(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('my/index');
        $I->click('Удалить');

        $I->dontSeeRecord(Offer::class, ['id' => 1]);
        $I->dontSeeRecord(Image::class, ['offer_id' => 1]);
    }

    public function userCannotDeleteForeignOffer(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/index', ['id' => 2]);
        $csrf = $I->grabValueFrom('input[name="_csrf"]');
        $I->sendAjaxPostRequest('/offers/delete?id=2', ['_csrf' => $csrf]);

        $I->seeResponseCodeIs(403);
        $I->seeRecord(Offer::class, ['id' => 2]);
    }

    public function missingCategoryReturns404(FunctionalTester $I): void
    {
        $I->amOnRoute('offers/category', ['id' => 999]);
        $I->seeResponseCodeIs(404);
    }
}
