<?php

namespace app\controllers;

use Yii;
use  yii\web\ForbiddenHttpException;

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
                'actions' => ['new', 'edit'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAdd()
    {
        return $this->render('add');
    }

    public function actionEdit()
    {
        return $this->render('edit');
    }

    public function actionCategory()
    {
        return $this->render('category');
    }

    public function actionCreateOffer()
    {
        if (Yii::$app->user->can('createOffer')) {
            // создание объявления
        } else {
            throw new ForbiddenHttpException('У вас нет прав для выполнения этого действия.');
        }
    }

    public function actionEditOffer()
    {
        if (Yii::$app->user->can('editOffer')) {
            // редактирование объявления
        } else {
            throw new ForbiddenHttpException('У вас нет прав для выполнения этого действия.');
        }
    }

    public function actionDeleteOffer()
    {
        if (Yii::$app->user->can('deleteOffer')) {
            // удаление объявления
        } else {
            throw new ForbiddenHttpException('У вас нет прав для выполнения этого действия.');
        }
    }
}
