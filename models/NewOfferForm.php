<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class NewOfferForm extends Model
{
    public $title;
    public $description;
    public $price;
    public $type;
    public $category_id;
    public $imageFiles;

    public function rules()
    {
        return [
            [['title', 'description', 'price', 'type', 'category_id'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['type'], 'string', 'max' => 255],
            [['category_id'], 'each', 'rule' => ['integer']],
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 4],
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
    
}
