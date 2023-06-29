<?php

use yii\db\Migration;

/**
 * Class m230629_140410_add_missing_fields
 */
class m230629_140410_add_missing_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'surname', $this->string()->notNull()->after('name'));
        $this->addColumn('{{%users}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%users}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        $this->addColumn('{{%offers}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%offers}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        $this->addColumn('{{%comments}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%comments}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        $this->createTable('{{%user_roles}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'role' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('fk_user_roles_user_id', '{{%user_roles}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_roles_user_id', '{{%user_roles}}');

        $this->dropTable('{{%user_roles}}');

        $this->dropColumn('{{%comments}}', 'updated_at');
        $this->dropColumn('{{%comments}}', 'created_at');

        $this->dropColumn('{{%offers}}', 'updated_at');
        $this->dropColumn('{{%offers}}', 'created_at');

        $this->dropColumn('{{%users}}', 'updated_at');
        $this->dropColumn('{{%users}}', 'created_at');
        $this->dropColumn('{{%users}}', 'surname');
    }
}
