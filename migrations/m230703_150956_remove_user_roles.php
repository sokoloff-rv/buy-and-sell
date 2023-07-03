<?php

use yii\db\Migration;

/**
 * Class m230703_150956_remove_user_roles
 */
class m230703_150956_remove_user_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Удаляем внешний ключ, связывающий таблицы "users" и "user_roles"
        $this->dropForeignKey('fk_user_roles_user_id', '{{%user_roles}}');

        // Удаляем таблицу "user_roles"
        $this->dropTable('{{%user_roles}}');

        // Добавляем поле "role" в таблицу "users"
        $this->addColumn('{{%users}}', 'role', $this->string(255)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Восстанавливаем таблицу "user_roles"
        $this->createTable('{{%user_roles}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'role' => $this->string(255)->notNull(),
        ]);

        // Добавляем внешний ключ, связывающий таблицы "users" и "user_roles"
        $this->addForeignKey('fk_user_roles_user_id', '{{%user_roles}}', 'user_id', '{{%users}}', 'id', 'CASCADE');

        // Удаляем поле "role" из таблицы "users"
        $this->dropColumn('{{%users}}', 'role');
    }
}
