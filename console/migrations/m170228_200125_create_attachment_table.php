<?php

use yii\db\Migration;

/**
 * Handles the creation of table `attachment`.
 */
class m170228_200125_create_attachment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('attachment', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255),
            'ext' => $this->string(255),
            'user_id' => $this->integer()->notNull(),
            'chat_id' => $this->integer()->notNull(),
            'message_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('attachment');
    }
}
