<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "offer_categories".
 *
 * @property int $id
 * @property int $offer_id
 * @property int $category_id
 *
 * @property Category $category
 * @property Offer $offer
 */
class OfferCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['offer_id', 'category_id'], 'required'],
            [['offer_id', 'category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['offer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offer_id' => 'Offer ID',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Offer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne(Offer::class, ['id' => 'offer_id']);
    }
}
