<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RegisterForm extends Model
{
    public $name;
    public $email;
    public $password;
    public $password_repeat;
    public $avatar;

    public function rules()
    {
        return [
            [['name', 'email', 'password', 'password_repeat', 'avatar'], 'required'],
            [['name'], 'match', 'pattern' => '/^[\p{L}\s]+$/u', 'message' => 'Имя и фамилия могут содержать только буквы и пробелы.'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            ['avatar', 'file', 'extensions' => 'png, jpg'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя и фамилия',
            'email' => 'Эл. почта',
            'password' => 'Пароль',
            'password_repeat' => 'Повторите пароль',
            'avatar' => 'Аватар',
        ];
    }
}
