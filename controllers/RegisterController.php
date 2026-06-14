<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\User;
use app\models\RegisterForm;

class RegisterController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
                'denyCallback' => fn () => $this->goHome(),
            ],
        ];
    }

    public function actionIndex()
    {
        $registerForm = new RegisterForm();
        $user = new User();

        if ($registerForm->load(Yii::$app->request->post())) {
            $registerForm->avatar = UploadedFile::getInstance($registerForm, 'avatar');

            if ($registerForm->validate()) {
                $avatarPath = null;
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $avatarPath = Yii::$app->imageStorage->save($registerForm->avatar);
                    $user->name = $registerForm->name;
                    $user->email = $registerForm->email;
                    $user->password = Yii::$app->getSecurity()->generatePasswordHash($registerForm->password);
                    $user->avatar = $avatarPath;
                    if (!$user->save()) {
                        throw new \RuntimeException('Не удалось сохранить пользователя.');
                    }
                    User::assignUserRole($user->id);
                    $transaction->commit();
                    return $this->redirect(['/login']);
                } catch (\Throwable $exception) {
                    if ($transaction->isActive) {
                        $transaction->rollBack();
                    }
                    if ($avatarPath !== null) {
                        try {
                            Yii::$app->imageStorage->delete($avatarPath);
                        } catch (\Throwable $cleanupException) {
                            Yii::warning($cleanupException);
                        }
                    }
                    Yii::error($exception);
                    $registerForm->addError('avatar', 'Не удалось создать аккаунт.');
                }
            }
        }

        return $this->render('index', [
            'registerForm' => $registerForm,
        ]);
    }
}
