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
            [['category_id'], 'each', 'rule' => ['integer']],
            [['imageFiles'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 5],
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
        $imageFiles = UploadedFile::getInstances($this, 'imageFiles');

        if ($this->validate()) {
            $newOffer = $this->newOffer();
            $newOffer->save(false);
            foreach ($this->category_id as $categoryId) {
                $category = Category::findOne($categoryId);
                if ($category) {
                    $newOffer->link('categories', $category);
                }
            }
            if ($imageFiles) {
                foreach ($imageFiles as $file) {
                    $newFileName = uniqid('upload') . '.' . $file->getExtension();
                    $file->saveAs('@webroot/uploads/' . $newFileName);
                    $imagePath = '/uploads/' . $newFileName;
                    Image::saveImage($imagePath, $newOffer->id);
                }
            }
            return $newOffer->id;
        }

        return false;
    }
    
}
