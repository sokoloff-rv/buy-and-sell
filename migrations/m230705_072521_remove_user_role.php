<?php

use yii\db\Migration;

/**
 * Class m230705_072521_remove_user_role
 */
class m230705_072521_remove_user_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('users', 'role');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('users', 'role', $this->string()->notNull());
    }
    */
}
