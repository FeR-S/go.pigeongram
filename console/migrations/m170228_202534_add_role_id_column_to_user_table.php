<?php

use yii\db\Migration;

/**
 * Handles adding role_id to table `user`.
 */
class m170228_202534_add_role_id_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'role_id', $this->smallInteger()->defaultValue(1));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'role_id');
    }
}
