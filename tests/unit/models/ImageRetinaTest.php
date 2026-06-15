<?php

namespace tests\unit\models;

use app\models\Image;
use Codeception\Test\Unit;
use Yii;

class ImageRetinaTest extends Unit
{
    private string $webroot;
    private string $originalWebroot;

    protected function _before(): void
    {
        $this->originalWebroot = Yii::getAlias('@webroot');
        $this->webroot = Yii::getAlias('@runtime/test-webroot');
        if (!is_dir($this->webroot . '/img')) {
            mkdir($this->webroot . '/img', 0775, true);
        }
        Yii::setAlias('@webroot', $this->webroot);
    }

    protected function _after(): void
    {
        foreach (glob($this->webroot . '/img/*') ?: [] as $file) {
            @unlink($file);
        }
        Yii::setAlias('@webroot', $this->originalWebroot);
    }

    public function testReturnsEmptyStringForPathWithoutExtension(): void
    {
        $this->assertSame('', Image::retinaUrl('/img/noextension'));
    }

    public function testReturnsEmptyStringWhenRetinaFileMissing(): void
    {
        $this->assertSame('', Image::retinaUrl('/img/blank.png'));
    }

    public function testReturnsRetinaUrlWhenRetinaFileExists(): void
    {
        file_put_contents($this->webroot . '/img/blank@2x.png', 'retina');

        $this->assertSame('/img/blank@2x.png', Image::retinaUrl('/img/blank.png'));
    }
}
