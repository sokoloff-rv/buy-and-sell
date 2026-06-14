<?php

class SmokeCest
{
    public function productionPagesWork(FunctionalTester $I): void
    {
        $I->amOnRoute('main/index');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Тестовое объявление пользователя');

        $I->amOnRoute('offers/index', ['id' => 1]);
        $I->seeResponseCodeIsSuccessful();
        $I->see('Тестовый комментарий достаточной длины.');

        $I->amOnRoute('offers/category', ['id' => 1]);
        $I->seeResponseCodeIsSuccessful();

        $I->amOnRoute('search/index', ['SearchForm' => ['query' => 'Тестовое']]);
        $I->seeResponseCodeIsSuccessful();
    }
}
