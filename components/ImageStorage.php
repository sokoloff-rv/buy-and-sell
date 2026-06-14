<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\web\UploadedFile;

class ImageStorage extends Component
{
    public string $uploadPath = '@webroot/uploads';
    public string $uploadUrl = '/uploads';

    public function save(UploadedFile $file): string
    {
        $directory = Yii::getAlias($this->uploadPath);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Не удалось создать директорию загрузок.');
        }

        $extension = strtolower($file->getExtension());
        $name = Yii::$app->security->generateRandomString(32) . '.' . $extension;
        $path = $directory . DIRECTORY_SEPARATOR . $name;
        if (!$file->saveAs($path)) {
            throw new \RuntimeException('Не удалось сохранить изображение.');
        }

        return rtrim($this->uploadUrl, '/') . '/' . $name;
    }

    public function saveMany(array $files): array
    {
        $paths = [];
        try {
            foreach ($files as $file) {
                $paths[] = $this->save($file);
            }
        } catch (\Throwable $exception) {
            $this->deleteMany($paths);
            throw $exception;
        }

        return $paths;
    }

    public function deleteMany(array $paths): void
    {
        foreach ($paths as $path) {
            try {
                $this->delete($path);
            } catch (\Throwable $exception) {
                Yii::warning($exception);
            }
        }
    }

    public function stageDeletion(array $paths): array
    {
        $trashDirectory = Yii::getAlias('@runtime/deleted-images');
        if (!is_dir($trashDirectory) && !mkdir($trashDirectory, 0775, true) && !is_dir($trashDirectory)) {
            throw new \RuntimeException('Не удалось создать временную директорию.');
        }

        $staged = [];
        try {
            foreach ($paths as $path) {
                $file = $this->resolveLocalPath($path);
                if ($file === null || !is_file($file)) {
                    continue;
                }
                $trash = $trashDirectory . DIRECTORY_SEPARATOR . Yii::$app->security->generateRandomString(32);
                if (!rename($file, $trash)) {
                    throw new \RuntimeException('Не удалось подготовить изображение к удалению.');
                }
                $staged[$file] = $trash;
            }
        } catch (\Throwable $exception) {
            $this->restoreStaged($staged);
            throw $exception;
        }

        return $staged;
    }

    public function restoreStaged(array $staged): void
    {
        $failed = false;
        foreach ($staged as $file => $trash) {
            if (is_file($trash) && !rename($trash, $file)) {
                $failed = true;
            }
        }
        if ($failed) {
            throw new \RuntimeException('Не удалось восстановить изображение.');
        }
    }

    public function purgeStaged(array $staged): void
    {
        $failed = false;
        foreach ($staged as $trash) {
            if (is_file($trash) && !unlink($trash)) {
                $failed = true;
            }
        }
        if ($failed) {
            throw new \RuntimeException('Не удалось окончательно удалить изображение.');
        }
    }

    public function delete(string $path): void
    {
        $file = $this->resolveLocalPath($path);
        if ($file !== null && is_file($file) && !unlink($file)) {
            throw new \RuntimeException('Не удалось удалить изображение.');
        }
    }

    private function resolveLocalPath(string $path): ?string
    {
        if ($path === '' || preg_match('~^https?://~i', $path)) {
            return null;
        }

        $relativePath = ltrim($path, '/');
        $uploadUrl = trim($this->uploadUrl, '/');
        if ($uploadUrl === '' || !str_starts_with($relativePath, $uploadUrl . '/')) {
            return null;
        }

        $uploadDirectory = realpath(Yii::getAlias($this->uploadPath));
        if ($uploadDirectory === false) {
            return null;
        }

        $file = $uploadDirectory . DIRECTORY_SEPARATOR . substr($relativePath, strlen($uploadUrl) + 1);
        $directory = realpath(dirname($file));
        if ($directory === false || ($directory !== $uploadDirectory && !str_starts_with($directory, $uploadDirectory . DIRECTORY_SEPARATOR))) {
            return null;
        }

        return $file;
    }
}
