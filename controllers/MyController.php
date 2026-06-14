<?php

namespace app\controllers;

use Yii;
use app\models\Offer;
use app\models\Comment;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class MyController extends AccessController
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-comment' => ['POST'],
                ],
            ],
        ]);
    }

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
            $dateComparison = $b['comments'][0]->created_at <=> $a['comments'][0]->created_at;
            return $dateComparison ?: $b['offer']->id <=> $a['offer']->id;
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
        if ($comment->offer->user_id != Yii::$app->user->id && !Yii::$app->user->can('deleteComment')) {
            throw new ForbiddenHttpException('Нельзя удалить комментарий к чужому объявлению.');
        }

        if ($comment->delete() === false) {
            throw new \RuntimeException('Не удалось удалить комментарий.');
        }
        Yii::$app->session->setFlash('success', 'Комментарий успешно удален.');

        return $this->redirect(['comments']);
    }
}
