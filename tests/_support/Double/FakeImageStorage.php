<?php

namespace Double;

use app\components\ImageStorage;
use Yii;
use yii\web\UploadedFile;

class FakeImageStorage extends ImageStorage
{
    public array $failOn = [];

    public ?int $failSaveOnCall = null;

    private int $saveCalls = 0;

    public function save(UploadedFile $file): string
    {
        $this->maybeFail('save');
        $this->saveCalls++;
        if ($this->failSaveOnCall === $this->saveCalls) {
            throw new \RuntimeException('FakeImageStorage: преднамеренная ошибка сохранения файла.');
        }

        $directory = Yii::getAlias($this->uploadPath);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Не удалось создать директорию загрузок.');
        }

        $extension = strtolower($file->getExtension());
        $name = Yii::$app->security->generateRandomString(32) . '.' . $extension;
        $path = $directory . DIRECTORY_SEPARATOR . $name;
        if (!copy($file->tempName, $path)) {
            throw new \RuntimeException('Не удалось сохранить изображение.');
        }

        return rtrim($this->uploadUrl, '/') . '/' . $name;
    }

    public function saveMany(array $files): array
    {
        $this->maybeFail('saveMany');

        return parent::saveMany($files);
    }

    public function stageDeletion(array $paths): array
    {
        $this->maybeFail('stageDeletion');

        return parent::stageDeletion($paths);
    }

    public function restoreStaged(array $staged): void
    {
        $this->maybeFail('restoreStaged');

        parent::restoreStaged($staged);
    }

    public function purgeStaged(array $staged): void
    {
        $this->maybeFail('purgeStaged');

        parent::purgeStaged($staged);
    }

    private function maybeFail(string $method): void
    {
        if (in_array($method, $this->failOn, true)) {
            throw new \RuntimeException("FakeImageStorage: преднамеренная ошибка в {$method}().");
        }
    }
}
