<?php

namespace app\models;

use Yii;

class Image extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'images';
    }

    public function rules()
    {
        return [
            [['offer_id', 'image_path'], 'required'],
            [['offer_id'], 'integer'],
            [['image_path'], 'string', 'max' => 255],
            [['offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['offer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offer_id' => 'Offer ID',
            'image_path' => 'Image Path',
        ];
    }

    public function getOffer()
    {
        return $this->hasOne(Offer::class, ['id' => 'offer_id']);
    }

    public static function saveImage(string $imagePath, int $offerId): void
    {
        $newImage = new self;
        $newImage->image_path = $imagePath;
        $newImage->offer_id = $offerId;
        $newImage->save(false);
    }
}
