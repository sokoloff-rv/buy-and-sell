<?php

use yii\db\Migration;

/**
 * Class m230705_071242_add_roles_and_permissions
 */
class m230705_071242_add_roles_and_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Создание ролей
        $user = $auth->createRole('user');
        $moderator = $auth->createRole('moderator');

        // Добавление ролей в RBAC систему
        $auth->add($user);
        $auth->add($moderator);

        // Создание разрешений
        $createOffer = $auth->createPermission('createOffer');
        $editOffer = $auth->createPermission('editOffer');
        $deleteOffer = $auth->createPermission('deleteOffer');

        // Добавление разрешений в RBAC систему
        $auth->add($createOffer);
        $auth->add($editOffer);
        $auth->add($deleteOffer);

        // Привязка разрешений к ролям
        $auth->addChild($user, $createOffer);
        $auth->addChild($moderator, $createOffer);
        $auth->addChild($moderator, $editOffer);
        $auth->addChild($moderator, $deleteOffer);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        // Удаление связей разрешений и ролей
        $auth->removeAllAssignments();

        // Удаление разрешений
        $auth->remove($auth->getPermission('createOffer'));
        $auth->remove($auth->getPermission('editOffer'));
        $auth->remove($auth->getPermission('deleteOffer'));

        // Удаление ролей
        $auth->remove($auth->getRole('user'));
        $auth->remove($auth->getRole('moderator'));
    }
}
