<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\User;
use app\models\LoginForm;
use app\models\VkEmailForm;

class LoginController extends Controller
{
    private const VK_REGISTRATION_SESSION = 'vkRegistration';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
                'denyCallback' => fn () => $this->goHome(),
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['POST'],
                ],
            ],
        ];
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionIndex()
    {
        $loginForm = new LoginForm();

        if ($loginForm->load(Yii::$app->request->post()) && $loginForm->validate()) {
            $user = User::findOne(['email' => $loginForm->email]);

            if (!$user) {
                $loginForm->addError('email', 'Такой пользователь не найден.');
            } elseif (!$user->password || !Yii::$app->getSecurity()->validatePassword($loginForm->password, $user->password)) {
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
        $email = $userAttributes['email'] ?? $accessToken->getParam('email');

        $foundUser = User::findOne(['vk_id' => $vkId]);
        if (!$foundUser && $email) {
            $foundUser = User::findOne(['email' => $email]);
        }

        if (!$foundUser && !$email) {
            Yii::$app->session->set(self::VK_REGISTRATION_SESSION, [
                'user_id' => $vkId,
                'first_name' => $userAttributes['first_name'] ?? '',
                'last_name' => $userAttributes['last_name'] ?? '',
                'avatar' => $userAttributes['avatar'] ?? null,
            ]);
            return $this->redirect(['/login/vk-email']);
        }

        $userAttributes['email'] = $email;

        if ($foundUser) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $foundUser->vk_id = $vkId;
                $foundUser->name = trim(($userAttributes['first_name'] ?? '') . ' ' . ($userAttributes['last_name'] ?? '')) ?: $foundUser->name;
                $foundUser->avatar = $userAttributes['avatar'] ?? $foundUser->avatar;
                if (!$foundUser->save()) {
                    throw new \Exception('Не удалось сохранить пользователя: ' . implode('; ', $foundUser->getFirstErrors()));
                }

                User::assignUserRole($foundUser->id);
                $transaction->commit();
            } catch (\Throwable $exception) {
                if ($transaction->isActive) {
                    $transaction->rollBack();
                }
                throw $exception;
            }

            Yii::$app->user->login($foundUser);
        } else {
            $newUser = new User();
            $newUser->createUserFromVK($userAttributes);
        }

        return $this->goHome();
    }

    public function actionVkEmail()
    {
        $userAttributes = Yii::$app->session->get(self::VK_REGISTRATION_SESSION);
        if (!$userAttributes) {
            return $this->redirect(['/login']);
        }

        $form = new VkEmailForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $userAttributes['email'] = $form->email;
            $user = new User();
            $user->createUserFromVK($userAttributes);
            Yii::$app->session->remove(self::VK_REGISTRATION_SESSION);

            return $this->goHome();
        }

        return $this->render('vk-email', [
            'form' => $form,
        ]);
    }
}
