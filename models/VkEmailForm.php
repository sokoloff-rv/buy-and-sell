<?php

namespace app\models;

use yii\base\Model;

class VkEmailForm extends Model
{
    public string $email = '';

    public function rules(): array
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот email уже используется. Войдите в существующий аккаунт другим способом.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => 'Эл. почта',
        ];
    }
}
