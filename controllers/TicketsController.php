<?php

namespace app\controllers;

class TicketsController extends AccessController
{
    public function getAccessRules(): array
    {
        return [
            [
                'actions' => ['index'],
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

    public function actionNew()
    {
        return $this->render('new');
    }

    public function actionEdit()
    {
        return $this->render('edit');
    }
}
