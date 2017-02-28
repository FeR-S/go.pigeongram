<?php

use yii\db\Migration;

/**
 * Handles the creation of table `bid`.
 */
class m170228_195418_create_bid_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('bid', [
            'id' => $this->primaryKey(),
            'user_id_from' => $this->integer()->notNull(),
            'user_id_to' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('bid');
    }
}
