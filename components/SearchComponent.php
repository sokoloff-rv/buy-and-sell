<?php

namespace app\components;

use app\models\SearchForm;

class SearchComponent extends \yii\base\Component
{
    public function getSearchModel()
    {
        $searchModel = new SearchForm();

        if ($searchModel->load(\Yii::$app->request->get()) && $searchModel->validate()) {
            // логика поиска
        }

        return $searchModel;
    }
}
