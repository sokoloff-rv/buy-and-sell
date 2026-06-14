<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Offer;
use app\models\Image;

class EditOfferForm extends Model
{
    public string $title = '';
    public string $description = '';
    public int $price = 0;
    public string $type = '';
    public array $category_id = [];
    public array $imageFiles = [];
    public bool $hasExistingImages = false;

    public function rules()
    {
        return [
            [['title', 'description', 'price', 'type', 'category_id'], 'required'],
            ['imageFiles', 'required', 'when' => fn (self $model): bool => !$model->hasExistingImages],
            [['title'], 'string', 'min' => 10, 'max' => 100],
            [['description'], 'string', 'min' => 50, 'max' => 1000],
            [['price'], 'number', 'min' => 100],
            [['type'], 'in', 'range' => [Offer::TYPE_BUY, Offer::TYPE_SELL]],
            [['category_id'], 'each', 'rule' => ['integer']],
            [['category_id'], 'each', 'rule' => ['exist', 'targetClass' => Category::class, 'targetAttribute' => 'id']],
            [['imageFiles'], 'file', 'extensions' => 'png, jpg', 'mimeTypes' => 'image/png, image/jpeg', 'checkExtensionByMimeType' => true, 'maxFiles' => 5],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Заголовок',
            'description' => 'Описание',
            'price' => 'Цена',
            'type' => 'Тип объявления',
            'category_id' => 'Категория',
            'imageFiles' => 'Изображение',
        ];
    }

    public function updateOffer(int $offerId): bool
    {
        $offer = Offer::findOne($offerId);

        if (!$offer) {
            return false;
        }

        $this->hasExistingImages = $offer->getImages()->exists();
        $this->imageFiles = UploadedFile::getInstances($this, 'imageFiles');

        if (!$this->validate()) {
            return false;
        }

        $newPaths = [];
        $oldPaths = [];
        $staged = [];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->imageFiles) {
                $newPaths = Yii::$app->imageStorage->saveMany($this->imageFiles);
                $oldPaths = array_column($offer->images, 'image_path');
                $staged = Yii::$app->imageStorage->stageDeletion($oldPaths);
            }
            $offer->title = $this->title;
            $offer->description = $this->description;
            $offer->price = $this->price;
            $offer->type = $this->type;
            if (!$offer->save()) {
                throw new \RuntimeException('Не удалось сохранить объявление.');
            }

            $offer->unlinkAll('categories', true);
            foreach ($this->category_id as $categoryId) {
                $category = Category::findOne($categoryId);
                if (!$category) {
                    throw new \RuntimeException('Категория не найдена.');
                }
                $offer->link('categories', $category);
            }

            if ($this->imageFiles) {
                if (Image::deleteAll(['offer_id' => $offerId]) !== count($oldPaths)) {
                    throw new \RuntimeException('Не удалось заменить изображения объявления.');
                }
                foreach ($newPaths as $imagePath) {
                    if (!Image::saveImage($imagePath, $offer->id)) {
                        throw new \RuntimeException('Не удалось сохранить изображение объявления.');
                    }
                }
            }

            $transaction->commit();
        } catch (\Throwable $exception) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }
            Yii::$app->imageStorage->deleteMany($newPaths);
            try {
                Yii::$app->imageStorage->restoreStaged($staged);
            } catch (\Throwable $restoreException) {
                Yii::error($restoreException);
            }
            Yii::error($exception);
            $this->addError('imageFiles', 'Не удалось сохранить объявление.');
            return false;
        }

        try {
            Yii::$app->imageStorage->purgeStaged($staged);
        } catch (\Throwable $exception) {
            Yii::warning($exception);
        }

        return true;
    }
}
