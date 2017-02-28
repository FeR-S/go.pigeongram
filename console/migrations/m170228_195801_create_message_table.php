<?php

use yii\db\Migration;

/**
 * Handles the creation of table `message`.
 */
class m170228_195801_create_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('message', [
            'id' => $this->primaryKey(),
            'message' => $this->text()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'chat_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull()->defaultValue(0),
            'attachment_id' => $this->integer(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('message');
    }
}
