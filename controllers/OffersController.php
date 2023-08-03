<?php

namespace app\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\models\NewOfferForm;
use app\models\EditOfferForm;
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
                'actions' => ['add', 'edit'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
    }

    public function actionIndex($id)
    {
        $offer = Offer::findOne($id);

        if ($offer === null) {
            throw new NotFoundHttpException('Объявление не найдено.');
        }

        return $this->render('index', [
            'offer' => $offer,
        ]);
    }

    public function actionAdd()
    {
        $newOfferForm = new NewOfferForm();
        if (Yii::$app->request->getIsPost()) {
            $newOfferForm->load(Yii::$app->request->post());
            $newOfferId = $newOfferForm->createOffer();
            if ($newOfferId) {
                return Yii::$app->response->redirect([
                    "/offers?id=$newOfferId"
                ]);
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
            if ($editOfferForm->updateOffer($offer)) {
                return Yii::$app->response->redirect([
                    "/offers?id=$id"
                ]);
            }
        }

        return $this->render('edit', [
            'offer' => $offer,
            'editOfferForm' => $editOfferForm,
        ]);
    }


    public function actionDelete()
    {
    }

    public function actionCategory()
    {
        return $this->render('category');
    }
}
