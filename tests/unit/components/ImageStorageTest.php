<?php

namespace tests\unit\components;

use Codeception\Test\Unit;
use Yii;

class ImageStorageTest extends Unit
{
    public function testDeleteIsLimitedToUploadDirectory(): void
    {
        $directory = Yii::getAlias('@runtime/test-uploads');
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $upload = $directory . '/delete-me.jpg';
        file_put_contents($upload, 'test');
        $static = Yii::getAlias('@app/web/img/blank.png');

        Yii::$app->imageStorage->delete('/img/blank.png');
        Yii::$app->imageStorage->delete('/test-uploads/delete-me.jpg');

        $this->assertFileExists($static);
        $this->assertFileDoesNotExist($upload);
    }
}
