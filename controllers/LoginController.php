<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\LoginForm;

class LoginController extends Controller
{
    public function actionIndex()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(['email' => $model->email]);

            if (!$user) {
                $model->addError('email', 'Такой пользователь не найден.');
            } elseif (!Yii::$app->getSecurity()->validatePassword($model->password, $user->password)) {
                $model->addError('password', 'Введен неверный пароль.');
            } else {
                Yii::$app->user->login($user);
                return $this->goHome();
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
