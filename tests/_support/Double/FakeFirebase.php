<?php

namespace Double;

use yii\base\Component;

class FakeFirebase extends Component
{
    public function getDatabase(): FakeFirebaseDatabase
    {
        return new FakeFirebaseDatabase();
    }

    public function createCustomToken(int $userId): string
    {
        return 'fake-custom-token-' . $userId;
    }
}
