<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChatForm extends Model
{
    public $message;

    public function rules()
    {
        return [
            ['message', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'message' => 'Ваше сообщение в чат',
        ];
    }
}
