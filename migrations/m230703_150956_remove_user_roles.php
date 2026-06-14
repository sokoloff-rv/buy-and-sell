<?php

use yii\db\Migration;

class m230703_150956_remove_user_roles extends Migration
{

    public function safeUp()
    {

        $this->dropForeignKey('fk_user_roles_user_id', '{{%user_roles}}');

        $this->dropTable('{{%user_roles}}');

        $this->addColumn('{{%users}}', 'role', $this->string(255)->notNull());
    }

    public function safeDown()
    {

        $this->createTable('{{%user_roles}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'role' => $this->string(255)->notNull(),
        ]);

        $this->addForeignKey('fk_user_roles_user_id', '{{%user_roles}}', 'user_id', '{{%users}}', 'id', 'CASCADE');

        $this->dropColumn('{{%users}}', 'role');
    }
}
