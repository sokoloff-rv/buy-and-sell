<?php

namespace tests\unit\models;

use app\commands\RoleController;
use app\models\User;
use Codeception\Test\Unit;
use Yii;
use yii\console\ExitCode;

class UserAndRoleTest extends Unit
{
    protected function _after(): void
    {
        Yii::$app->user->setIdentity(null);
    }

    private function roleController(): RoleController
    {
        return new RoleController('role', Yii::$app);
    }

    public function testCreateUserFromVkCreatesPasswordlessUserAssignsRoleAndLogsIn(): void
    {
        $user = new User();
        $user->createUserFromVK([
            'user_id' => 555111,
            'first_name' => 'Иван',
            'last_name' => 'Петров',
            'email' => 'vk-new@example.com',
            'avatar' => 'https://example.com/a.jpg',
        ]);

        $created = User::findOne(['vk_id' => 555111]);
        $this->assertNotNull($created);
        $this->assertSame('Иван Петров', $created->name);
        $this->assertSame('vk-new@example.com', $created->email);
        $this->assertNull($created->password);
        $this->assertNotNull(Yii::$app->authManager->getAssignment('user', (string) $created->id));
        $this->assertSame($created->id, Yii::$app->user->id);
    }

    public function testCreateUserFromVkRollsBackWhenEmailNotUnique(): void
    {
        $user = new User();

        try {
            $user->createUserFromVK([
                'user_id' => 666222,
                'first_name' => 'Дубликат',
                'last_name' => 'Почты',
                'email' => 'user@example.com', // уже занят в фикстурах
            ]);
            $this->fail('Ожидалось исключение из-за неуникального email.');
        } catch (\Throwable $exception) {
        }

        $this->assertNull(User::findOne(['vk_id' => 666222]));
    }

    public function testAssignUserRoleIsIdempotent(): void
    {
        User::assignUserRole(1);
        User::assignUserRole(1);

        $this->assertNotNull(Yii::$app->authManager->getAssignment('user', '1'));
    }

    public function testRoleControllerAssignsModeratorIdempotentlyAndRejectsMissingUser(): void
    {
        $auth = Yii::$app->authManager;
        $controller = $this->roleController();

        $this->assertSame(ExitCode::OK, $controller->actionModerator('user@example.com'));
        $this->assertNotNull($auth->getAssignment('moderator', '1'));

        $this->assertSame(ExitCode::OK, $controller->actionModerator('user@example.com'));

        $this->assertSame(ExitCode::DATAERR, $controller->actionModerator('missing@example.com'));
    }

    public function testRoleControllerRevokesModeratorButKeepsUserRole(): void
    {
        $auth = Yii::$app->authManager;
        $controller = $this->roleController();

        $this->assertSame(ExitCode::OK, $controller->actionRevokeModerator('moderator@example.com'));
        $this->assertNull($auth->getAssignment('moderator', '2'));
        $this->assertNotNull($auth->getAssignment('user', '2'));
    }
}
