<?php

namespace app\controllers;

use Yii;
use app\models\Offer;
use app\models\Comment;

class MyController extends AccessController
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $offers = Offer::find()->where(['user_id' => $userId])->orderBy(['created_at' => SORT_DESC])->all();

        return $this->render('index', [
            'offers' => $offers,
        ]);
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

    public function actionDeleteComment($id)
    {
        $comment = Comment::findOne($id);

        if ($comment !== null && $comment->offer->user_id == Yii::$app->user->id) {
            $comment->delete();
            Yii::$app->session->setFlash('success', 'Комментарий успешно удален.');
        } else {
            Yii::$app->session->setFlash('error', 'Комментарий не найден или у вас нет прав на его удаление.');
        }

        return $this->redirect(['comments']);
    }
}
