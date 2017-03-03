<?php

use yii\db\Migration;

class m170302_082959_add_user_table extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),

            'first_name' => $this->string(255),
            'last_name' => $this->string(255),

            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'role_id' => $this->smallInteger()->notNull()->defaultValue(2),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
