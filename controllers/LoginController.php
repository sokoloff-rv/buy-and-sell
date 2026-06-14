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
        $loginForm = new LoginForm();

        if ($loginForm->load(Yii::$app->request->post()) && $loginForm->validate()) {
            $user = User::findOne(['email' => $loginForm->email]);

            if (!$user) {
                $loginForm->addError('email', 'Такой пользователь не найден.');
            } elseif (!Yii::$app->getSecurity()->validatePassword($loginForm->password, $user->password)) {
                $loginForm->addError('password', 'Введен неверный пароль.');
            } else {
                Yii::$app->user->login($user);
                return $this->goHome();
            }
        }

        return $this->render('index', [
            'loginForm' => $loginForm,
        ]);
    }

    public function actionVk()
    {
        $url = Yii::$app->authClientCollection->getClient("vkid")->buildAuthUrl();
        Yii::$app->getResponse()->redirect($url);
    }

    public function actionVkAuth()
    {
        $client = Yii::$app->authClientCollection->getClient("vkid");

        $code = Yii::$app->request->get('code');
        // VK ID возвращает device_id на redirect; он обязателен при обмене кода на токен.
        $deviceId = Yii::$app->request->get('device_id');
        $accessToken = $client->fetchAccessToken($code, ['device_id' => $deviceId]);
        if (!$accessToken) {
            throw new \Exception('Не удалось получить токен доступа от VK.');
        }

        $userAttributes = $client->getUserAttributes();
        if (!$userAttributes) {
            throw new \Exception('Не удалось получить атрибуты пользователя от VK.');
        }

        $vkId = $userAttributes['user_id'];
        // VK ID отдаёт email не в user_info, а в ответе на обмен кода на токен.
        $email = $userAttributes['email'] ?? $accessToken->getParam('email');
        $userAttributes['email'] = $email;

        // Сначала ищем по vk_id — он всегда присутствует в ответе VK ID.
        // Если пользователя по vk_id нет, но есть email, связываем существующий
        // аккаунт, ранее зарегистрированный по этой почте.
        $foundUser = User::findOne(['vk_id' => $vkId]);
        if (!$foundUser && $email) {
            $foundUser = User::findOne(['email' => $email]);
        }

        if ($foundUser) {
            $foundUser->vk_id = $vkId;
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
