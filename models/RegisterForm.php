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
            // все поля обязательны
            [['name', 'email', 'password', 'password_repeat'], 'required'],
            // имя не должно содержать цифр и специальных символов, кроме пробела
            [['name'], 'match', 'pattern' => '/^[a-zA-Zа-яА-ЯёЁ\s\-]*$/', 'message' => 'Имя и фамилия заполнены некорректно.'],
            // email должен быть валидным адресом электронной почты
            ['email', 'email'],
            // email должен быть уникальным
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот адрес электронной почты уже занят.'],
            // пароль должен содержать не менее 6 символов
            ['password', 'string', 'min' => 6],
            // пароль и повтор пароля должны совпадать
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают.'],
            // avatar должен быть файлом изображения
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

    public function register()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->avatar = $this->avatar;
        $user->generateAuthKey();
        
        return $user->save();
    }
}
