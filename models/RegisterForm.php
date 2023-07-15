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

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'password_repeat'], 'required'],
            [['name'], 'match', 'pattern' => '/^[a-zA-Zа-яА-ЯёЁ\s\-]*$/', 'message' => 'Имя и фамилия заполнены некорректно.'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот адрес электронной почты уже занят.'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают.'],
            ['avatar', 'file', 'extensions' => 'png, jpg, jpeg, gif'],
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
