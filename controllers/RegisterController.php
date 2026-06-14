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
                $uniqueFileName = uniqid() . '_' . $registerForm->avatar->baseName . '.' . $registerForm->avatar->extension;
                $filePath = Yii::getAlias('@webroot/uploads/' . $uniqueFileName);
                $registerForm->avatar->saveAs($filePath);

                $user->name = $registerForm->name;
                $user->email = $registerForm->email;
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($registerForm->password);
                $user->avatar = '/uploads/' . $uniqueFileName;
                $user->created_at = date('Y-m-d H:i:s');
                $user->updated_at = date('Y-m-d H:i:s');

                if ($user->save()) {
                    User::assignUserRole($user->id);
                    return $this->redirect(['/login']);
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось сохранить файл.');
                }
            }
        }

        return $this->render('index', [
            'registerForm' => $registerForm,
        ]);
    }
}
