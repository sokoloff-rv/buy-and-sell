<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Offer;
use yii\web\Controller;
use yii\web\HttpException;

class MainController extends Controller
{
    public function actionIndex()
    {
        $newOffers = Offer::find()
            ->with(['images', 'categories'])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(8)
            ->all();

        return $this->render('index', [
            'categories' => Category::findWithOfferCounts(true),
            'newOffers' => $newOffers,
            'mostDiscussed' => Offer::getMostDiscussed(8),
        ]);
    }

    public function actionError()
    {
        $this->layout = false;
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $statusCode = $exception instanceof HttpException ? $exception->statusCode : 500;
            if ($statusCode === 403) {
                Yii::$app->response->statusCode = 403;
                return $this->render('error403');
            }
            if ($statusCode === 404) {
                Yii::$app->response->statusCode = 404;
                return $this->render('error404');
            }
            if ($statusCode >= 400 && $statusCode < 500) {
                Yii::$app->response->statusCode = $statusCode;
                return $this->render('error4xx', ['statusCode' => $statusCode]);
            }

            Yii::error($exception);
            Yii::$app->response->statusCode = 500;
            return $this->render('error500');
        }

        return '';
    }
}
