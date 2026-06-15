<?php

namespace tests\unit\models;

use app\models\EditOfferForm;
use app\models\Image;
use app\models\NewOfferForm;
use app\models\Offer;
use app\models\User;
use Codeception\Test\Unit;
use Double\FakeImageStorage;
use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class OfferTransactionTest extends Unit
{
    private string $uploadDir;

    private array $tempFiles = [];

    protected function _before(): void
    {
        $this->uploadDir = Yii::getAlias('@runtime/test-uploads');
        $this->clearDir($this->uploadDir);
        $this->clearDir(Yii::getAlias('@runtime/deleted-images'));

        Yii::$app->set('imageStorage', [
            'class' => FakeImageStorage::class,
            'uploadPath' => '@runtime/test-uploads',
            'uploadUrl' => '/test-uploads',
        ]);
        Yii::$app->user->setIdentity(User::findOne(1));
    }

    protected function _after(): void
    {
        Yii::$app->user->setIdentity(null);
        $_FILES = [];
        UploadedFile::reset();
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }
        $this->tempFiles = [];
        $this->clearDir($this->uploadDir);
        $this->clearDir(Yii::getAlias('@runtime/deleted-images'));
    }

    public function testCreateOfferStoresRecordsCategoriesImagesAndFile(): void
    {
        $this->injectImageFiles('NewOfferForm', 1);
        $form = new NewOfferForm([
            'title' => 'Новое объявление для теста',
            'description' => str_repeat('Описание объявления. ', 5),
            'price' => 500,
            'type' => 'sell',
            'category_id' => [1],
        ]);

        $offerId = $form->createOffer();

        $this->assertIsInt($offerId);
        $offer = Offer::findOne($offerId);
        $this->assertNotNull($offer);
        $this->assertSame(1, (int) $offer->user_id);
        $this->assertCount(1, $offer->categories);
        $this->assertCount(1, $offer->images);
        $this->assertFileExists($this->uploadDir . DIRECTORY_SEPARATOR . basename($offer->images[0]->image_path));
    }

    public function testCreateOfferRollsBackAndDeletesFilesWhenSaveFails(): void
    {
        Yii::$app->user->setIdentity(null);
        $this->injectImageFiles('NewOfferForm', 1);
        $form = new NewOfferForm([
            'title' => 'Объявление с откатом',
            'description' => str_repeat('Описание объявления. ', 5),
            'price' => 500,
            'type' => 'sell',
            'category_id' => [1],
        ]);

        $result = $form->createOffer();

        $this->assertFalse($result);
        $this->assertArrayHasKey('imageFiles', $form->errors);
        $this->assertNull(Offer::findOne(['title' => 'Объявление с откатом']));
        $this->assertSame([], glob($this->uploadDir . DIRECTORY_SEPARATOR . '*'));
    }

    public function testCreateOfferReturnsFalseWhenStorageFails(): void
    {
        Yii::$app->imageStorage->failOn = ['saveMany'];
        $this->injectImageFiles('NewOfferForm', 1);
        $form = new NewOfferForm([
            'title' => 'Объявление без хранилища',
            'description' => str_repeat('Описание объявления. ', 5),
            'price' => 500,
            'type' => 'sell',
            'category_id' => [1],
        ]);

        $this->assertFalse($form->createOffer());
        $this->assertNull(Offer::findOne(['title' => 'Объявление без хранилища']));
        $this->assertSame([], glob($this->uploadDir . DIRECTORY_SEPARATOR . '*'));
    }

    public function testUpdateOfferReplacesImagesAndStoresFile(): void
    {
        $oldPath = $this->makeOfferImageLocal(1, 'old-image.png');
        $this->injectImageFiles('EditOfferForm', 1);
        $form = new EditOfferForm([
            'title' => 'Обновлённое объявление',
            'description' => str_repeat('Новое описание объявления. ', 4),
            'price' => 1700,
            'type' => 'sell',
            'category_id' => [1],
        ]);

        $this->assertTrue($form->updateOffer(1));

        $offer = Offer::findOne(1);
        $this->assertSame('Обновлённое объявление', $offer->title);
        $this->assertCount(1, $offer->images);
        $newPath = $offer->images[0]->image_path;
        $this->assertStringStartsWith('/test-uploads/', $newPath);
        $this->assertFileExists($this->uploadDir . DIRECTORY_SEPARATOR . basename($newPath));
        $this->assertFileDoesNotExist($oldPath);
    }

    public function testUpdateOfferRollsBackAndRestoresOnFailure(): void
    {
        $oldPath = $this->makeOfferImageLocal(1, 'old-image.png');
        Event::on(Offer::class, ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'cancelEvent']);
        $this->injectImageFiles('EditOfferForm', 1);
        $form = new EditOfferForm([
            'title' => 'Объявление с неудачным обновлением',
            'description' => str_repeat('Новое описание объявления. ', 4),
            'price' => 1700,
            'type' => 'sell',
            'category_id' => [1],
        ]);

        try {
            $this->assertFalse($form->updateOffer(1));
        } finally {
            Event::off(Offer::class, ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'cancelEvent']);
        }

        $offer = Offer::findOne(1);
        $this->assertSame('Тестовое объявление пользователя', $offer->title);
        $this->assertSame('/test-uploads/old-image.png', $offer->images[0]->image_path);
        $this->assertFileExists($oldPath);
        $this->assertSame([$oldPath], glob($this->uploadDir . DIRECTORY_SEPARATOR . '*'));
    }

    public function testDeleteWithFilesRemovesOfferAndPurgesFile(): void
    {
        [$offer, $diskPath] = $this->offerWithUploadedImage();

        $this->assertTrue($offer->deleteWithFiles());

        $this->assertNull(Offer::findOne($offer->id));
        $this->assertSame(0, Image::find()->where(['offer_id' => $offer->id])->count());
        $this->assertFileDoesNotExist($diskPath);
    }

    public function testDeleteWithFilesRollsBackAndRestoresFileOnFailure(): void
    {
        [$offer, $diskPath] = $this->offerWithUploadedImage();
        $offer->on(ActiveRecord::EVENT_BEFORE_DELETE, static function ($event): void {
            $event->isValid = false;
        });

        try {
            $offer->deleteWithFiles();
            $this->fail('Ожидалось исключение при неудачном удалении.');
        } catch (\Throwable $exception) {
        }

        $this->assertNotNull(Offer::findOne($offer->id));
        $this->assertFileExists($diskPath);
    }

    private function offerWithUploadedImage(): array
    {
        $offer = new Offer([
            'user_id' => 1,
            'title' => 'Объявление с файлом',
            'description' => str_repeat('Описание объявления. ', 5),
            'type' => 'sell',
            'price' => 999,
        ]);
        $this->assertTrue($offer->save());

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
        $name = 'offer-image-' . $offer->id . '.png';
        $diskPath = $this->uploadDir . DIRECTORY_SEPARATOR . $name;
        copy(codecept_data_dir('test-image.png'), $diskPath);
        Image::saveImage('/test-uploads/' . $name, $offer->id);
        $offer->populateRelation('images', $offer->getImages()->all());

        return [$offer, $diskPath];
    }

    public function cancelEvent($event): void
    {
        $event->isValid = false;
    }

    private function makeOfferImageLocal(int $offerId, string $name): string
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
        $diskPath = $this->uploadDir . DIRECTORY_SEPARATOR . $name;
        copy(codecept_data_dir('test-image.png'), $diskPath);
        Image::updateAll(['image_path' => '/test-uploads/' . $name], ['offer_id' => $offerId]);

        return $diskPath;
    }

    private function injectImageFiles(string $formName, int $count = 1): void
    {
        $names = $types = $tmp = $errors = $sizes = [];
        for ($i = 0; $i < $count; $i++) {
            $base = tempnam(sys_get_temp_dir(), 'upl');
            $temp = $base . '.png';
            @unlink($base);
            copy(codecept_data_dir('test-image.png'), $temp);
            $this->tempFiles[] = $temp;
            $names[] = 'photo.png';
            $types[] = 'image/png';
            $tmp[] = $temp;
            $errors[] = UPLOAD_ERR_OK;
            $sizes[] = filesize($temp);
        }

        $_FILES[$formName] = [
            'name' => ['imageFiles' => $names],
            'type' => ['imageFiles' => $types],
            'tmp_name' => ['imageFiles' => $tmp],
            'error' => ['imageFiles' => $errors],
            'size' => ['imageFiles' => $sizes],
        ];
        UploadedFile::reset();
    }

    private function clearDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
