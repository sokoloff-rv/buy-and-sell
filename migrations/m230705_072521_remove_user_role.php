<?php

use yii\db\Migration;

class m230705_072521_remove_user_role extends Migration
{

    public function safeUp()
    {
        $this->dropColumn('{{%users}}', 'role');
    }

    public function safeDown()
    {
        $this->addColumn('{{%users}}', 'role', $this->string()->notNull());
    }
}
