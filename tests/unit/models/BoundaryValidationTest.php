<?php

namespace tests\unit\models;

use app\models\NewCommentForm;
use app\models\NewOfferForm;
use app\models\RegisterForm;
use Codeception\Test\Unit;

class BoundaryValidationTest extends Unit
{
    public function testOfferTitleLengthBoundary(): void
    {
        $short = new NewOfferForm(['title' => str_repeat('а', 9)]);
        $short->validate(['title']);
        $this->assertArrayHasKey('title', $short->errors);

        $ok = new NewOfferForm(['title' => str_repeat('а', 10)]);
        $ok->validate(['title']);
        $this->assertArrayNotHasKey('title', $ok->errors);
    }

    public function testOfferDescriptionLengthBoundary(): void
    {
        $short = new NewOfferForm(['description' => str_repeat('а', 49)]);
        $short->validate(['description']);
        $this->assertArrayHasKey('description', $short->errors);

        $ok = new NewOfferForm(['description' => str_repeat('а', 50)]);
        $ok->validate(['description']);
        $this->assertArrayNotHasKey('description', $ok->errors);
    }

    public function testOfferPriceMinimumBoundary(): void
    {
        $low = new NewOfferForm(['price' => 99]);
        $low->validate(['price']);
        $this->assertArrayHasKey('price', $low->errors);

        $ok = new NewOfferForm(['price' => 100]);
        $ok->validate(['price']);
        $this->assertArrayNotHasKey('price', $ok->errors);
    }

    public function testOfferCategoryMustExist(): void
    {
        $missing = new NewOfferForm(['category_id' => [999]]);
        $missing->validate(['category_id']);
        $this->assertArrayHasKey('category_id', $missing->errors);

        $ok = new NewOfferForm(['category_id' => [1]]);
        $ok->validate(['category_id']);
        $this->assertArrayNotHasKey('category_id', $ok->errors);
    }

    public function testOfferTypeMustBeInRange(): void
    {
        $invalid = new NewOfferForm(['type' => 'rent']);
        $invalid->validate(['type']);
        $this->assertArrayHasKey('type', $invalid->errors);

        $ok = new NewOfferForm(['type' => 'buy']);
        $ok->validate(['type']);
        $this->assertArrayNotHasKey('type', $ok->errors);
    }

    public function testCommentMinimumLength(): void
    {
        $short = new NewCommentForm(['text' => str_repeat('а', 19)]);
        $this->assertFalse($short->validate());
        $this->assertArrayHasKey('text', $short->errors);

        $ok = new NewCommentForm(['text' => str_repeat('а', 20)]);
        $this->assertTrue($ok->validate());
    }

    public function testRegisterNameAcceptsOnlyLettersAndSpaces(): void
    {
        $invalid = new RegisterForm(['name' => 'Иван123']);
        $invalid->validate(['name']);
        $this->assertArrayHasKey('name', $invalid->errors);

        $ok = new RegisterForm(['name' => 'Иван Петров']);
        $ok->validate(['name']);
        $this->assertArrayNotHasKey('name', $ok->errors);
    }
}
