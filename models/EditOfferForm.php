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

    public function rules()
    {
        return [
            [['title'], 'string', 'min' => 10, 'max' => 100],
            [['description'], 'string', 'min' => 50, 'max' => 1000],
            [['price'], 'number', 'min' => 100],
            [['type'], 'safe'],
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

    public function updateOffer(): int|bool
    {
        return true;
    }
    
}
