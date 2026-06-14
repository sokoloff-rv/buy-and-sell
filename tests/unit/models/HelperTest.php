<?php

namespace tests\unit\models;

use app\models\Offer;
use app\models\User;
use Codeception\Test\Unit;
use Yii;

class HelperTest extends Unit
{
    public function testAvatarUrls(): void
    {
        $user = new User();
        $this->assertSame('/img/avatar.jpg', $user->avatarUrl);

        $user->avatar = 'uploads/avatar.jpg';
        $this->assertSame('/uploads/avatar.jpg', $user->avatarUrl);

        $user->avatar = 'https://example.com/avatar.jpg';
        $this->assertSame('https://example.com/avatar.jpg', $user->avatarUrl);
    }

    public function testOfferPresentationHelpers(): void
    {
        $offer = new Offer([
            'type' => Offer::TYPE_SELL,
            'description' => str_repeat('а', 60),
        ]);

        $this->assertSame('Продам', $offer->label);
        $this->assertSame(55, mb_strlen($offer->announcement));
        $this->assertSame('/img/blank.png', $offer->previewImage);
    }

    public function testModeratorPermissionsAreSeparate(): void
    {
        $auth = Yii::$app->authManager;

        $this->assertFalse($auth->checkAccess(1, 'deleteOffer'));
        $this->assertFalse($auth->checkAccess(1, 'deleteComment'));
        $this->assertTrue($auth->checkAccess(2, 'deleteOffer'));
        $this->assertTrue($auth->checkAccess(2, 'deleteComment'));
    }
}
