<?php

namespace app\models;

use Yii;
use yii\base\Model;

class CommentForm extends Model
{
    public $text;

    public function rules()
    {
        return [
            [['text'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Текст комментария',
        ];
    }
}
