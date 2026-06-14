<?php

use yii\db\Migration;

class m260614_220000_project_improvement_indexes extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_offers_created_at_id', '{{%offers}}', ['created_at', 'id']);
        $this->createIndex('idx_comments_offer_created_at_id', '{{%comments}}', ['offer_id', 'created_at', 'id']);
        $this->createIndex('uq_offer_categories_offer_category', '{{%offer_categories}}', ['offer_id', 'category_id'], true);
    }

    public function safeDown()
    {
        $this->dropIndex('uq_offer_categories_offer_category', '{{%offer_categories}}');
        $this->dropIndex('idx_comments_offer_created_at_id', '{{%comments}}');
        $this->dropIndex('idx_offers_created_at_id', '{{%offers}}');
    }
}
