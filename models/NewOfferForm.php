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
            [['title', 'description', 'price', 'type', 'category_id'], 'required', 'message' => 'Нужно заполнить.'],
            [['description'], 'string', 'message' => 'Описание должно быть строкой.'],
            [['price'], 'number', 'message' => 'Цена должна быть числом.'],
            [['type'], 'string', 'max' => 255],
            [['category_id'], 'each', 'rule' => ['integer']],
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 4, 'uploadRequired' => 'Изображение обязательно для загрузки.'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) { 
            foreach ($this->imageFiles as $file) {
                $file->saveAs('uploads/' . $file->baseName . '.' . $file->extension);
            }
            return true;
        } else {
            return false;
        }
    }
}
