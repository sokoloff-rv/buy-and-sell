<?php

namespace tests\unit\models;

use app\models\Category;
use app\models\Comment;
use app\models\Offer;
use Codeception\Test\Unit;
use yii\helpers\ArrayHelper;

class OfferQueryTest extends Unit
{
    private function makeOffer(string $title): Offer
    {
        $offer = new Offer([
            'user_id' => 1,
            'title' => $title,
            'description' => str_repeat('Описание объявления. ', 5),
            'type' => 'sell',
            'price' => 500,
        ]);
        $offer->save(false);

        return $offer;
    }

    private function addComments(int $offerId, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $comment = new Comment([
                'user_id' => 1,
                'offer_id' => $offerId,
                'text' => 'Комментарий достаточной длины для теста.',
            ]);
            $comment->save(false);
        }
    }

    public function testGetMostDiscussedOrdersByCommentCountAndSkipsOffersWithoutComments(): void
    {
        $high = $this->makeOffer('Обсуждаемое объявление');
        $this->addComments($high->id, 3);
        $silent = $this->makeOffer('Объявление без комментариев');

        $result = Offer::getMostDiscussed(10);
        $ids = ArrayHelper::getColumn($result, 'id');

        $this->assertSame($high->id, $result[0]->id);
        $this->assertNotContains($silent->id, $ids);
        $this->assertNotContains(2, $ids);
        $this->assertContains(1, $ids);
    }

    public function testGetMostDiscussedRespectsLimit(): void
    {
        $high = $this->makeOffer('Самое обсуждаемое объявление');
        $this->addComments($high->id, 5);

        $result = Offer::getMostDiscussed(1);

        $this->assertCount(1, $result);
        $this->assertSame($high->id, $result[0]->id);
    }

    public function testFindWithOfferCountsCountsOffersAndIncludesEmptyCategories(): void
    {
        $empty = new Category(['name' => 'Авто', 'image' => '/img/blank.png']);
        $empty->save(false);

        $categories = Category::findWithOfferCounts();
        $counts = ArrayHelper::map($categories, 'id', 'offers_count');

        $this->assertSame(2, (int) $counts[1]);
        $this->assertSame(0, (int) $counts[$empty->id]);

        $names = ArrayHelper::getColumn($categories, 'name');
        $this->assertSame('Авто', $names[0]);
    }
}
