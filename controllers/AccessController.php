<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

abstract class AccessController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function ($rule, $action) {
                    if (\Yii::$app->user->isGuest) {
                        return $this->redirect(['/login']);
                    }

                    throw new ForbiddenHttpException('У вас нет доступа к этой странице.');
                },
                'rules' => $this->getAccessRules(),
            ],
        ];
    }

    public function getAccessRules(): array
    {
        return [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
    }
}
