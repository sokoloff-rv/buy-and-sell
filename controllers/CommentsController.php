<?php

namespace app\controllers;

use yii\web\Controller;

class CommentsController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
