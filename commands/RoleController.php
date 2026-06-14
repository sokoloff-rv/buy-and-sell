<?php

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class RoleController extends Controller
{
    public function actionModerator(string $email): int
    {
        $user = User::findOne(['email' => $email]);
        if (!$user) {
            $this->stderr("Пользователь с email {$email} не найден.\n");
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole('moderator');
        if (!$role) {
            $this->stderr("Роль moderator не настроена.\n");
            return ExitCode::CONFIG;
        }

        if (!$auth->getAssignment('moderator', (string) $user->id)) {
            $auth->assign($role, $user->id);
        }

        $this->stdout("Роль moderator назначена пользователю {$email}.\n");
        return ExitCode::OK;
    }

    public function actionRevokeModerator(string $email): int
    {
        $user = User::findOne(['email' => $email]);
        if (!$user) {
            $this->stderr("Пользователь с email {$email} не найден.\n");
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole('moderator');
        if (!$role) {
            $this->stderr("Роль moderator не настроена.\n");
            return ExitCode::CONFIG;
        }

        $auth->revoke($role, $user->id);
        User::assignUserRole($user->id);

        $this->stdout("Роль moderator снята с пользователя {$email}.\n");
        return ExitCode::OK;
    }
}
