<?php

use yii\db\Migration;

/**
 * Handles the creation of table `buddies`.
 */
class m170228_194942_create_buddy_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('buddy', [
            'id' => $this->primaryKey(),
            'buddy_1' => $this->integer()->notNull(),
            'buddy_2' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('buddies');
    }
}
