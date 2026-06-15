<?php

namespace tests\unit\components;

use app\components\ImageStorage;
use Codeception\Test\Unit;
use Double\FakeImageStorage;
use Yii;
use yii\web\UploadedFile;

class ImageStorageTest extends Unit
{
    private string $uploadDir;
    private string $trashDir;
    private array $tempFiles = [];

    protected function _before(): void
    {
        $this->uploadDir = Yii::getAlias('@runtime/test-uploads');
        $this->trashDir = Yii::getAlias('@runtime/deleted-images');
        $this->clearDir($this->uploadDir);
        $this->clearDir($this->trashDir);
    }

    protected function _after(): void
    {
        $this->clearDir($this->uploadDir);
        $this->clearDir($this->trashDir);
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }
        $this->tempFiles = [];
    }

    private function realStorage(): ImageStorage
    {
        return new ImageStorage([
            'uploadPath' => '@runtime/test-uploads',
            'uploadUrl' => '/test-uploads',
        ]);
    }

    private function fakeStorage(): FakeImageStorage
    {
        return new FakeImageStorage([
            'uploadPath' => '@runtime/test-uploads',
            'uploadUrl' => '/test-uploads',
        ]);
    }

    private function uploadedPng(): UploadedFile
    {
        $base = tempnam(sys_get_temp_dir(), 'upl');
        $temp = $base . '.png';
        @unlink($base);
        copy(codecept_data_dir('test-image.png'), $temp);
        $this->tempFiles[] = $temp;

        return new UploadedFile([
            'name' => 'photo.png',
            'tempName' => $temp,
            'type' => 'image/png',
            'size' => filesize($temp),
            'error' => UPLOAD_ERR_OK,
        ]);
    }

    private function makeUploadFile(string $name): string
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
        $path = $this->uploadDir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, 'image-bytes');

        return $path;
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

    public function testSaveStoresFileAndReturnsUrl(): void
    {
        $url = $this->fakeStorage()->save($this->uploadedPng());

        $this->assertStringStartsWith('/test-uploads/', $url);
        $this->assertStringEndsWith('.png', $url);
        $this->assertFileExists($this->uploadDir . DIRECTORY_SEPARATOR . basename($url));
    }

    public function testSaveManyStoresAllFiles(): void
    {
        $paths = $this->fakeStorage()->saveMany([$this->uploadedPng(), $this->uploadedPng()]);

        $this->assertCount(2, $paths);
        foreach ($paths as $path) {
            $this->assertFileExists($this->uploadDir . DIRECTORY_SEPARATOR . basename($path));
        }
    }

    public function testSaveManyRemovesEarlierFilesWhenOneFails(): void
    {
        $storage = $this->fakeStorage();
        $storage->failSaveOnCall = 2;

        try {
            $storage->saveMany([$this->uploadedPng(), $this->uploadedPng()]);
            $this->fail('Ожидалось исключение при сбое сохранения.');
        } catch (\RuntimeException $exception) {
        }

        $this->assertSame([], glob($this->uploadDir . DIRECTORY_SEPARATOR . '*'));
    }

    public function testStageRestorePurgeRoundTrip(): void
    {
        $storage = $this->realStorage();
        $original = $this->makeUploadFile('round-trip.jpg');

        $staged = $storage->stageDeletion(['/test-uploads/round-trip.jpg']);
        $this->assertFileDoesNotExist($original);
        $this->assertCount(1, $staged);

        $storage->restoreStaged($staged);
        $this->assertFileExists($original);

        $stagedAgain = $storage->stageDeletion(['/test-uploads/round-trip.jpg']);
        $storage->purgeStaged($stagedAgain);
        $this->assertFileDoesNotExist($original);
        foreach ($stagedAgain as $trash) {
            $this->assertFileDoesNotExist($trash);
        }
    }

    public function testStageDeletionIgnoresRemoteAndForeignPaths(): void
    {
        $storage = $this->realStorage();

        $staged = $storage->stageDeletion([
            'https://example.com/avatar.jpg',
            '/img/blank.png',
            '',
        ]);

        $this->assertSame([], $staged);
    }

    public function testDeleteIsLimitedToUploadDirectory(): void
    {
        $upload = $this->makeUploadFile('delete-me.jpg');
        $static = Yii::getAlias('@app/web/img/blank.png');

        $storage = $this->realStorage();
        $storage->delete('/img/blank.png');
        $storage->delete('/test-uploads/delete-me.jpg');

        $this->assertFileExists($static);
        $this->assertFileDoesNotExist($upload);
    }

    public function testDeleteRejectsTraversalOutsideUploads(): void
    {
        $storage = $this->realStorage();
        $secret = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'storage-secret.txt';
        file_put_contents($secret, 'secret');

        try {
            $storage->delete('/test-uploads/../storage-secret.txt');
            $this->assertFileExists($secret);
        } finally {
            @unlink($secret);
        }
    }
}
