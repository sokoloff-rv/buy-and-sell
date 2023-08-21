<?php

namespace app\models;

use yii\base\Model;

class SearchForm extends Model
{
    public $query;

    public function rules()
    {
        return [
            ['query', 'string'],
            ['query', 'trim'],
        ];
    }
}
