<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required', 'message' => 'Логин и пароль введены неверно.'],
            ['email', 'email', 'message' => 'Некорректный адрес эл. почты.'],
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
