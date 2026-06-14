<?php

use yii\db\Migration;

class m260614_233000_widen_users_avatar extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%users}}', 'avatar', $this->string(1024));
    }

    public function safeDown()
    {
        $this->alterColumn('{{%users}}', 'avatar', $this->string());
    }
}
