<?php

namespace app\models;

use Yii;

class Offer extends \yii\db\ActiveRecord
{
    const TYPE_SELL = 'sell';
    const TYPE_BUY = 'buy';

    public static function tableName()
    {
        return 'offers';
    }

    public function rules()
    {
        return [
            [['user_id', 'title', 'description', 'type', 'price'], 'required'],
            [['user_id'], 'integer'],
            [['description', 'type'], 'string'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'description' => 'Description',
            'type' => 'Type',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['offer_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]);
    }

    public function getImages()
    {
        return $this->hasMany(Image::class, ['offer_id' => 'id']);
    }

    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('offer_categories', ['offer_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getLabel()
    {
        return $this->type === self::TYPE_SELL ? 'Продам' : 'Куплю';
    }

    public static function getMostDiscussed($limit = 4)
    {
        return self::find()
            ->alias('offers')
            ->with(['images', 'categories'])
            ->innerJoin('comments', 'comments.offer_id = offers.id')
            ->groupBy('offers.id')
            ->orderBy(['COUNT(comments.id)' => SORT_DESC, 'offers.created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    public function getPreviewImage(): string
    {
        return $this->images[0]->image_path ?? '/img/blank.png';
    }

    public function getAnnouncement(): string
    {
        return mb_strlen($this->description) > 55
            ? mb_substr($this->description, 0, 54) . '…'
            : $this->description;
    }
}
