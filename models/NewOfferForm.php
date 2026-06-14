<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Offer;
use app\models\Image;

class NewOfferForm extends Model
{
    public string $title = '';
    public string $description = '';
    public int $price = 0;
    public string $type = '';
    public array $category_id = [];
    public array $imageFiles = [];

    public function rules()
    {
        return [
            [['title', 'description', 'price', 'type', 'category_id', 'imageFiles'], 'required'],
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

    public function newOffer(): Offer
    {
        $offer = new Offer;

        $offer->title = $this->title;
        $offer->description = $this->description;
        $offer->price = $this->price;
        $offer->type = $this->type;
        $offer->user_id = Yii::$app->user->getId();

        return $offer;
    }

    public function createOffer(): int|bool
    {
        $this->imageFiles = UploadedFile::getInstances($this, 'imageFiles');
        if (!$this->validate()) {
            return false;
        }

        $imagePaths = [];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $imagePaths = Yii::$app->imageStorage->saveMany($this->imageFiles);
            $newOffer = $this->newOffer();
            if (!$newOffer->save()) {
                throw new \RuntimeException('Не удалось сохранить объявление.');
            }
            foreach ($this->category_id as $categoryId) {
                $category = Category::findOne($categoryId);
                if (!$category) {
                    throw new \RuntimeException('Категория не найдена.');
                }
                $newOffer->link('categories', $category);
            }
            foreach ($imagePaths as $imagePath) {
                if (!Image::saveImage($imagePath, $newOffer->id)) {
                    throw new \RuntimeException('Не удалось сохранить изображение объявления.');
                }
            }
            $transaction->commit();
            return $newOffer->id;
        } catch (\Throwable $exception) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }
            Yii::$app->imageStorage->deleteMany($imagePaths);
            Yii::error($exception);
            $this->addError('imageFiles', 'Не удалось сохранить объявление.');
            return false;
        }
    }
}
