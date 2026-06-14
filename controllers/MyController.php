<?php

namespace app\controllers;

use Yii;
use app\models\Offer;
use app\models\Comment;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class MyController extends AccessController
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $offers = Offer::find()
            ->with(['images', 'categories'])
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'offers' => $offers,
        ]);
    }

    public function actionComments()
    {
        $offers = Offer::find()
            ->with(['comments' => function ($query) {
                $query->with('user')->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]);
            }])
            ->where(['user_id' => Yii::$app->user->id])
            ->all();

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
            return $b['comments'][0]->created_at <=> $a['comments'][0]->created_at;
        });

        return $this->render('comments', [
            'offersWithComments' => $offersWithComments
        ]);
    }

    public function actionDeleteComment($id)
    {
        $comment = Comment::findOne($id);

        if ($comment === null) {
            throw new NotFoundHttpException('Комментарий не найден.');
        }
        if ($comment->offer->user_id != Yii::$app->user->id && !Yii::$app->user->can('moderator')) {
            throw new ForbiddenHttpException('Нельзя удалить комментарий к чужому объявлению.');
        }

        $comment->delete();
        Yii::$app->session->setFlash('success', 'Комментарий успешно удален.');

        return $this->redirect(['comments']);
    }
}
