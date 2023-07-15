<?php

use yii\db\Migration;

/**
 * Class m230715_072521_remove_user_surname
 */
class m230715_072521_remove_user_surname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('users', 'surname');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('users', 'surname', $this->string()->notNull());
    }
}
