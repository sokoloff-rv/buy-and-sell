<?php

namespace app\models;

use Yii;

class Category extends \yii\db\ActiveRecord
{
    public $offers_count = 0;

    public static function tableName()
    {
        return 'categories';
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
            ->viaTable('offer_categories', ['category_id' => 'id']);
    }

    public static function findWithOfferCounts(bool $onlyWithOffers = false): array
    {
        $query = self::find()
            ->select(['categories.*', 'COUNT(offer_categories.offer_id) AS offers_count'])
            ->leftJoin('offer_categories', 'offer_categories.category_id = categories.id')
            ->groupBy('categories.id')
            ->orderBy(['categories.name' => SORT_ASC]);

        if ($onlyWithOffers) {
            $query->having(['>', 'COUNT(offer_categories.offer_id)', 0]);
        }

        return $query->all();
    }

    public function getRandomImageUrl(): string
    {
        $files = glob(Yii::getAlias('@webroot/img/cat*.jpg')) ?: [];
        $files = array_values(array_filter($files, static function (string $file): bool {
            return !str_contains($file, '@2x') && !str_contains($file, 'cat-s');
        }));

        if (!$files) {
            return $this->image ?: '/img/blank.png';
        }

        return '/img/' . basename($files[array_rand($files)]);
    }
}
