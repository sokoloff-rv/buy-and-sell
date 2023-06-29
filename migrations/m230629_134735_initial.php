<?php

use yii\db\Migration;

/**
 * Class m230629_134735_initial
 */
class m230629_134735_initial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull(),
            'avatar' => $this->string(),
            'vk_id' => $this->string()->unique(),
        ], $tableOptions);

        $this->createTable('{{%offers}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'type' => "ENUM('buy', 'sell') NOT NULL",
            'price' => $this->decimal(10, 2)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'image' => $this->string(),
        ], $tableOptions);

        $this->createTable('{{%offer_categories}}', [
            'id' => $this->primaryKey(),
            'offer_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%images}}', [
            'id' => $this->primaryKey(),
            'offer_id' => $this->integer()->notNull(),
            'image_path' => $this->string()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%comments}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'offer_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_offers_user_id', '{{%offers}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_offer_categories_offer_id', '{{%offer_categories}}', 'offer_id', '{{%offers}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_offer_categories_category_id', '{{%offer_categories}}', 'category_id', '{{%categories}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_images_offer_id', '{{%images}}', 'offer_id', '{{%offers}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_user_id', '{{%comments}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_offer_id', '{{%comments}}', 'offer_id', '{{%offers}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_comments_offer_id', '{{%comments}}');
        $this->dropForeignKey('fk_comments_user_id', '{{%comments}}');
        $this->dropForeignKey('fk_images_offer_id', '{{%images}}');
        $this->dropForeignKey('fk_offer_categories_category_id', '{{%offer_categories}}');
        $this->dropForeignKey('fk_offer_categories_offer_id', '{{%offer_categories}}');
        $this->dropForeignKey('fk_offers_user_id', '{{%offers}}');

        $this->dropTable('{{%comments}}');
        $this->dropTable('{{%images}}');
        $this->dropTable('{{%offer_categories}}');
        $this->dropTable('{{%categories}}');
        $this->dropTable('{{%offers}}');
        $this->dropTable('{{%users}}');
    }
}
