<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class MainController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionError()
    {
        $this->layout = false; 
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $statusCode = $exception->statusCode;
            if ($statusCode == 404) {
                return $this->render('error404');
            } else {
                return $this->render('error500');
            }
        }
    }
}
