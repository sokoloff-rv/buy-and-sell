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
            [['email', 'password'], 'required'],
            ['email', 'email'],
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
