<?php

use yii\db\Migration;

class m230705_071242_add_roles_and_permissions extends Migration
{

    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $user = $auth->createRole('user');
        $moderator = $auth->createRole('moderator');

        $auth->add($user);
        $auth->add($moderator);

        $createOffer = $auth->createPermission('createOffer');
        $editOffer = $auth->createPermission('editOffer');
        $deleteOffer = $auth->createPermission('deleteOffer');

        $auth->add($createOffer);
        $auth->add($editOffer);
        $auth->add($deleteOffer);

        $auth->addChild($user, $createOffer);
        $auth->addChild($moderator, $createOffer);
        $auth->addChild($moderator, $editOffer);
        $auth->addChild($moderator, $deleteOffer);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAllAssignments();

        $auth->remove($auth->getPermission('createOffer'));
        $auth->remove($auth->getPermission('editOffer'));
        $auth->remove($auth->getPermission('deleteOffer'));

        $auth->remove($auth->getRole('user'));
        $auth->remove($auth->getRole('moderator'));
    }
}
