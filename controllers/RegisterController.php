<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\User;
use app\models\RegisterForm;

class RegisterController extends Controller
{
    public function actionIndex()
    {
        $registerForm = new RegisterForm();
        $user = new User();

        if ($registerForm->load(Yii::$app->request->post())) {
            $user->avatar = UploadedFile::getInstance($registerForm, 'avatar');

            if ($registerForm->validate()) {
                if ($user->avatar) {
                    $uniqueFileName = uniqid() . '_' . $user->avatar->baseName . '.' . $user->avatar->extension;
                    $filePath = 'uploads/' . $uniqueFileName;
                    $user->avatar->saveAs($filePath);
                    $user->avatar = $filePath;
                }

                $user->name = $registerForm->name;
                $user->email = $registerForm->email;
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($registerForm->password);
                $user->created_at = date('Y-m-d H:i:s');
                $user->updated_at = date('Y-m-d H:i:s');

                if ($user->save()) {
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
