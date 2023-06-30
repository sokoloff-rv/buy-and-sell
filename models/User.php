<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $password
 * @property string|null $avatar
 * @property string|null $vk_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Comment[] $comments
 * @property Offer[] $offers
 * @property UserRole[] $userRoles
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'email', 'password'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'surname', 'email', 'password', 'avatar', 'vk_id'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['vk_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'surname' => 'Surname',
            'email' => 'Email',
            'password' => 'Password',
            'avatar' => 'Avatar',
            'vk_id' => 'Vk ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Comment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Offer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany(Offer::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserRole]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoles()
    {
        return $this->hasMany(UserRole::class, ['user_id' => 'id']);
    }
}
