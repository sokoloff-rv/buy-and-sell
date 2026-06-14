<?php

use yii\db\Migration;

class m260614_190000_tz_required_rbac_and_vk_password extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%users}}', 'password', $this->string()->null());

        $auth = Yii::$app->authManager;
        $user = $auth->getRole('user');
        $moderator = $auth->getRole('moderator');

        if (!$user) {
            $user = $auth->createRole('user');
            $auth->add($user);
        }
        if (!$moderator) {
            $moderator = $auth->createRole('moderator');
            $auth->add($moderator);
        }

        $deleteComment = $auth->getPermission('deleteComment');
        if (!$deleteComment) {
            $deleteComment = $auth->createPermission('deleteComment');
            $auth->add($deleteComment);
        }
        if (!$auth->hasChild($moderator, $deleteComment)) {
            $auth->addChild($moderator, $deleteComment);
        }

        foreach ((new \yii\db\Query())->select('id')->from('{{%users}}')->column() as $userId) {
            if (!$auth->getAssignment('user', (string) $userId)) {
                $auth->assign($user, $userId);
            }
        }
    }

    public function safeDown()
    {
        $this->alterColumn('{{%users}}', 'password', $this->string()->notNull());

        $auth = Yii::$app->authManager;
        $deleteComment = $auth->getPermission('deleteComment');
        if ($deleteComment) {
            $auth->remove($deleteComment);
        }
    }
}
