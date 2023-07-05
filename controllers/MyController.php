<?php

namespace app\controllers;

use yii\web\Controller;

class MyController extends AccessController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionComments()
    {
        return $this->render('comments');
    }
}
