<?php

namespace app\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\models\NewOfferForm;
use app\models\Offer;

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
                    "/offers"
                ]);
            }
        }

        return $this->render('add', [
            'newOfferForm' => $newOfferForm,
        ]);
    }

    public function actionEdit()
    {
        return $this->render('edit');
    }

    public function actionDelete()
    {
        
    }

    public function actionCategory()
    {
        return $this->render('category');
    }
}
