<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\VkUser;
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

    public function actionVk()
    {
        $url = Yii::$app->authClientCollection->getClient("vkontakte")->buildAuthUrl();
        Yii::$app->getResponse()->redirect($url);
    }

    public function actionVkAuth()
    {
        $client = Yii::$app->authClientCollection->getClient("vkontakte");
        $code = Yii::$app->request->get('code');
        $accessToken = $client->fetchAccessToken($code);
        $userAttributes = $client->getUserAttributes();

        $foundUser = User::findOne(['email' => $userAttributes['email']]);
        if ($foundUser) {
            $foundUser->vk_id = $userAttributes['user_id'];
            $foundUser->save();
            Yii::$app->user->login($foundUser);
            return $this->goHome();
        } else {
            $vkUser = new VkUser();
            $vkUser->createUser($userAttributes);
            return $this->goHome();
        }

        return $this->goHome();
    }
}
