<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class TestController extends Controller
{
    public function actionDbCheck()
    {
        try {
            Yii::$app->db->createCommand("SELECT 1")->queryScalar();
            echo 'Успешное соединение с базой данных!';
        } catch (\yii\db\Exception $e) {
            echo 'Не удалось соединиться с базой данных: ' . $e->getMessage();
        }
    }
}
