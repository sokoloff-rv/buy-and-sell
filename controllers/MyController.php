<?php

namespace app\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\Controller;
use app\models\Offer;

class MyController extends AccessController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionComments()
    {
        $offers = Offer::find()->where(['user_id' => Yii::$app->user->id])->all();

        $offersWithComments = [];
        foreach ($offers as $offer) {
            $comments = $offer->comments;
            if (!empty($comments)) {
                $offersWithComments[] = [
                    'offer' => $offer,
                    'comments' => $comments
                ];
            }
        }

        usort($offersWithComments, function ($a, $b) {
            return end($b['comments'])->created_at <=> end($a['comments'])->created_at;
        });

        return $this->render('comments', [
            'offersWithComments' => $offersWithComments
        ]);
    }
}
