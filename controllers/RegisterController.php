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
        $model = new RegisterForm();
        $user = new User();

        if ($model->load(Yii::$app->request->post())) {
            $user->avatar = UploadedFile::getInstance($model, 'avatar');

            if ($model->validate()) {
                if ($user->avatar) {
                    $filePath = 'uploads/' . $user->avatar->baseName . '.' . $user->avatar->extension;
                    $user->avatar->saveAs($filePath);
                    $user->avatar = $filePath;
                }

                $user->name = $model->name;
                $user->email = $model->email;
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($model->password);
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
            'model' => $model,
        ]);
    }
}
