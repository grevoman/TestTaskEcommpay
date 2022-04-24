<?php

namespace console\modules\partnerAnalytics\migrations;

use yii\db\Migration;

/**
 * Class m220424_061316_partner_analytic_data
 */
class m220424_061316_partner_analytic_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner_analytic_data}}', [
            'id' => $this->primaryKey(),
            'orderedAt' => $this->datetime()->notNull(),
            'partnerId' => $this->integer()->notNull(),
            'product' => $this->string()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'price' => $this->float()->notNull(),
            'deliveryType' => $this->string()->notNull(),
            'deliveryCity' => $this->string(),
            'deliveryCost' => $this->float(),
            'total' => $this->float()->notNull(),
            'reportDate' => $this->datetime()->notNull(),
            'createdAt' => $this->datetime()->notNull(),
            'updatedAt' => $this->datetime()->notNull(),
        ]);

        $this->createIndex('partnerId', '{{%partner_analytic_data}}', ['partnerId']);

        $this->addForeignKey(
            'fk-partner_analytic_data-partner',
            '{{%partner_analytic_data}}',
            'partnerId',
            '{{%partner}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-partner_analytic_data-partner', '{{%partner_analytic_data}}');
        $this->dropTable('{{%partner_analytic_data}}');
    }
}
