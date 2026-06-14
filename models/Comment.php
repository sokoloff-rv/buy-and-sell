<?php

namespace app\models;

use Yii;

class Comment extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'comments';
    }

    public function rules()
    {
        return [
            [['user_id', 'offer_id', 'text'], 'required'],
            [['user_id', 'offer_id'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['offer_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'offer_id' => 'Offer ID',
            'text' => 'Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getOffer()
    {
        return $this->hasOne(Offer::class, ['id' => 'offer_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
