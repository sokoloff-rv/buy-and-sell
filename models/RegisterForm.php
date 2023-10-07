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
            [['name'], 'match', 'pattern' => '/^[^\d!$%^&*()_+|~=`{}\[\]:";\'<>?,.\/]*$/'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            ['avatar', 'file', 'extensions' => 'png, jpg, jpeg'],
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
