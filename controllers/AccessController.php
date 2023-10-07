<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

abstract class AccessController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    return $this->redirect('/login');
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
