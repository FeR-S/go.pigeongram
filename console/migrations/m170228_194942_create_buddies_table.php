<?php

use yii\db\Migration;

/**
 * Handles the creation of table `buddies`.
 */
class m170228_194942_create_buddies_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('buddies', [
            'id' => $this->primaryKey(),
            'buddie_1' => $this->integer()->notNull(),
            'buddie_2' => $this->integer()->notNull(),
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
