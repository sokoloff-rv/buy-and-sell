<?php

namespace app\controllers;

use yii\web\Controller;

class MyTicketsController extends Controller extends AccessController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
