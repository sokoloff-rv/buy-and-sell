<?php

namespace app\controllers;

use yii\web\Controller;

class SearchController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
