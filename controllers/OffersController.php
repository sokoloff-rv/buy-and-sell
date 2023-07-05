<?php

namespace app\controllers;

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
}
