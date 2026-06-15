<?php

namespace tests\unit\components;

use app\components\FirebaseComponent;
use Codeception\Test\Unit;
use yii\base\InvalidConfigException;

class FirebaseComponentTest extends Unit
{
    public function testMissingCredentialsThrowsConfigException(): void
    {
        $component = new FirebaseComponent([
            'credentialsPath' => '',
            'databaseUri' => 'https://example.firebaseio.com',
        ]);

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Firebase service account JSON');
        $component->getDatabase();
    }

    public function testMissingDatabaseUriThrowsConfigException(): void
    {
        $component = new FirebaseComponent([
            'credentialsPath' => codecept_data_dir('test-image.png'),
            'databaseUri' => '',
        ]);

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Realtime Database URI');
        $component->getDatabase();
    }
}
