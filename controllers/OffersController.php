<?php

namespace app\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\models\NewOfferForm;
use app\models\EditOfferForm;
use app\models\NewCommentForm;
use app\models\Offer;
use yii\helpers\ArrayHelper;

class OffersController extends AccessController
{
    public function getAccessRules(): array
    {
        return [
            [
                'actions' => ['index', 'category'],
                'allow' => true,
                'roles' => ['?', '@'],
            ],
            [
                'actions' => ['add', 'edit', 'delete'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
    }

    public function actionIndex($id)
    {
        $offer = Offer::findOne($id);
        $comments = $offer->comments;

        if ($offer === null) {
            throw new NotFoundHttpException('Объявление не найдено.');
        }

        $newCommentForm = new NewCommentForm();
        if (Yii::$app->request->isPost && $newCommentForm->load(Yii::$app->request->post())) {
            if ($newCommentForm->createComment($id)) {
                Yii::$app->session->setFlash('success', 'Комментарий успешно добавлен.');
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка при добавлении комментария.');
            }
            return $this->refresh();
        }

        return $this->render('index', [
            'offer' => $offer,
            'comments' => $comments,
            'newCommentForm' => $newCommentForm,
        ]);
    }


    public function actionAdd()
    {
        $newOfferForm = new NewOfferForm();
        if (Yii::$app->request->getIsPost()) {
            $newOfferForm->load(Yii::$app->request->post());
            $newOfferId = $newOfferForm->createOffer();
            if ($newOfferId) {
                Yii::$app->session->setFlash('success', 'Объявление успешно добавлено.');
                return Yii::$app->response->redirect(["/offers?id=$newOfferId"]);
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка при добавлении объявления.');
            }
        }

        return $this->render('add', [
            'newOfferForm' => $newOfferForm,
        ]);
    }

    public function actionEdit($id)
    {
        $offer = Offer::findOne($id);
        if ($offer === null) {
            throw new NotFoundHttpException('Объявление не найдено.');
        }

        $editOfferForm = new EditOfferForm();
        if (Yii::$app->request->getIsPost()) {
            $editOfferForm->load(Yii::$app->request->post());
            if ($editOfferForm->updateOffer($offer->id)) {
                Yii::$app->session->setFlash('success', 'Объявление успешно обновлено.');
                return Yii::$app->response->redirect(["/offers?id=$id"]);
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка при обновлении объявления.');
            }
        }

        return $this->render('edit', [
            'offer' => $offer,
            'editOfferForm' => $editOfferForm,
        ]);
    }

    public function actionDelete($id)
    {
        $offer = Offer::findOne($id);
        if (!$offer) {
            throw new NotFoundHttpException('Объявление не найдено.');
        }
        $offer->delete();

        return $this->goHome();
    }

    public function actionCategory()
    {
        return $this->render('category');
    }
}
