<?php

use yii\db\Migration;

class m230715_072521_remove_user_surname extends Migration
{

    public function safeUp()
    {
        $this->dropColumn('users', 'surname');
    }

    public function safeDown()
    {
        $this->addColumn('users', 'surname', $this->string()->notNull());
    }
}
