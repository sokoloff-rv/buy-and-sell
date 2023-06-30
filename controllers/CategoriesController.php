<?php

namespace app\controllers;

use yii\web\Controller;

class CategoriesController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
