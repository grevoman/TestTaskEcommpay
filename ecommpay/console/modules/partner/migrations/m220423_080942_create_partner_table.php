<?php

namespace console\modules\partner\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner}}`.
 */
class m220423_080942_create_partner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'createdAt' => $this->datetime()->notNull(),
            'updatedAt' => $this->datetime()->notNull(),
        ]);

        $this->createIndex('idx-name', '{{%partner}}', ['name']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partner}}');
    }
}
