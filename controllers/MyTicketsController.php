<?php

namespace app\controllers;

use yii\web\Controller;

class MyTicketsController extends AccessController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
