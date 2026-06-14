<?php

namespace app\models;

class Category extends \yii\db\ActiveRecord
{
    public $offers_count = 0;

    public static function tableName()
    {
        return '{{%categories}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'image'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'image' => 'Image',
        ];
    }

    public function getOffers()
    {
        return $this->hasMany(Offer::class, ['id' => 'offer_id'])
            ->viaTable('{{%offer_categories}}', ['category_id' => 'id']);
    }

    public static function findWithOfferCounts(): array
    {
        return self::find()
            ->alias('categories')
            ->select(['categories.*', 'COUNT(offer_categories.offer_id) AS offers_count'])
            ->leftJoin('{{%offer_categories}} offer_categories', 'offer_categories.category_id = categories.id')
            ->groupBy('categories.id')
            ->orderBy(['categories.name' => SORT_ASC, 'categories.id' => SORT_ASC])
            ->all();
    }

    public function getImageUrl(): string
    {
        return $this->image ?: '/img/blank.png';
    }

    public function getRetinaImageUrl(): string
    {
        $image = $this->getImageUrl();
        $dot = strrpos($image, '.');

        return $dot === false ? $image : substr($image, 0, $dot) . '@2x' . substr($image, $dot);
    }
}
