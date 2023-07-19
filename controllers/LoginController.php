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
        if (!$accessToken) {
            throw new \Exception('Не удалось получить токен доступа от VK.');
        }

        $userAttributes = $client->getUserAttributes();
        if (!$userAttributes) {
            throw new \Exception('Не удалось получить атрибуты пользователя от VK.');
        }

        $foundUser = User::findOne(['email' => $userAttributes['email']]);
        if ($foundUser) {
            $foundUser->vk_id = $userAttributes['user_id'];
            if (!$foundUser->save()) {
                throw new \Exception('Не удалось сохранить пользователя.');
            }
            
            Yii::$app->user->login($foundUser);
        } else {
            $newUser = new User();
            $newUser->createUserFromVK($userAttributes);
        }

        return $this->goHome();
    }
}
