<?php

namespace app\controllers;

use yii\web\Controller;

class TicketsController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionNew()
    {
        return $this->render('new');
    }

    public function actionEdit()
    {
        return $this->render('edit');
    }
}
