<?php

use app\models\Category;
use app\models\Comment;
use app\models\Offer;

class CatalogCest
{
    private function makeOffer(string $title, ?string $createdAt = null): Offer
    {
        $offer = new Offer([
            'user_id' => 1,
            'title' => $title,
            'description' => str_repeat('Описание объявления. ', 5),
            'type' => 'sell',
            'price' => 500,
        ]);
        $offer->save(false);
        if ($createdAt !== null) {
            Offer::updateAll(['created_at' => $createdAt, 'updated_at' => $createdAt], ['id' => $offer->id]);
            $offer->refresh();
        }

        return $offer;
    }

    private function makeCategory(string $name): Category
    {
        $category = new Category(['name' => $name, 'image' => '/img/blank.png']);
        $category->save(false);

        return $category;
    }

    public function mainPageShowsCategoriesAndNewestOffers(FunctionalTester $I): void
    {
        $I->amOnRoute('main/index');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Тестовая категория');
        $I->see('Тестовое объявление пользователя');
    }

    public function mainPageShowsEightNewestOffersInOrder(FunctionalTester $I): void
    {
        for ($i = 1; $i <= 9; $i++) {
            $this->makeOffer("Свежесть {$i}", sprintf('2026-01-%02d 12:00:00', $i));
        }

        $I->amOnRoute('main/index');
        $section = '//section[.//p[contains(., "Самое свежее")]]';
        $I->seeNumberOfElements($section . '//li[contains(@class, "tickets-list__item")]', 8);
        $I->see('Свежесть 9', $section);
        $I->dontSee('Свежесть 1', $section);
        $titles = array_map('trim', $I->grabMultiple($section . '//h3[contains(@class, "ticket-card__title")]'));
        $I->assertLessThan(array_search('Свежесть 8', $titles, true), array_search('Свежесть 9', $titles, true));
    }

    public function mainPageOrdersMostDiscussedAndHidesEmptyBlock(FunctionalTester $I): void
    {
        $most = $this->makeOffer('Самое обсуждаемое');
        for ($i = 0; $i < 2; $i++) {
            (new Comment([
                'user_id' => 1,
                'offer_id' => $most->id,
                'text' => 'Комментарий достаточной длины для сортировки.',
            ]))->save(false);
        }

        $I->amOnRoute('main/index');
        $section = '//section[.//p[contains(., "Самые обсуждаемые")]]';
        $titles = array_map('trim', $I->grabMultiple($section . '//h3[contains(@class, "ticket-card__title")]'));
        $I->assertSame('Самое обсуждаемое', $titles[0]);

        Comment::deleteAll();
        $I->amOnRoute('main/index');
        $I->dontSee('Самые обсуждаемые');
    }

    public function searchReturnsMatchingOffersOnly(FunctionalTester $I): void
    {
        $older = $this->makeOffer('Горный велосипед старый', '2026-01-01 12:00:00');
        $newer = $this->makeOffer('Горный велосипед новый', '2026-01-02 12:00:00');

        $I->amOnRoute('search/index', ['SearchForm' => ['query' => 'велосипед']]);
        $I->seeNumberOfElements('.search-results__item', 2);
        $I->see('Найдено');
        $I->seeInField('SearchForm[query]', 'велосипед');
        $source = $I->grabPageSource();
        $I->assertLessThan(strpos($source, $older->title), strpos($source, $newer->title));
    }

    public function searchShowsEmptyState(FunctionalTester $I): void
    {
        $I->amOnRoute('search/index', ['SearchForm' => ['query' => 'НесуществующийТовар12345']]);
        $I->see('Не найдено ни одного объявления');
    }

    public function categoryShowsOnlyItsOffers(FunctionalTester $I): void
    {
        $category = $this->makeCategory('Электроника');
        $offer = $this->makeOffer('Телефон в категории Электроника');
        $offer->link('categories', $category);

        $I->amOnRoute('offers/category', ['id' => $category->id]);
        $I->see('Телефон в категории Электроника');
        $I->dontSee('Тестовое объявление пользователя');
    }

    public function categoryPaginatesByEight(FunctionalTester $I): void
    {
        $category = $this->makeCategory('Многотоварная категория');
        for ($i = 1; $i <= 9; $i++) {
            $offer = $this->makeOffer("Объявление в большой категории №{$i}");
            $offer->link('categories', $category);
        }

        $I->amOnRoute('offers/category', ['id' => $category->id]);
        $I->seeNumberOfElements('.tickets-list__item', 8);
        $I->seeElement('.pagination');
        $I->see('9', '.tickets-list__title');
    }
}
