<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'email', 'message' => 'Такой пользователь не найден.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Эл. почта',
            'password' => 'Пароль',
        ];
    }
}
