<?php

use yii\db\Migration;

class m160831_080324_update_admin_leoyan extends Migration
{
    public function up()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			// http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
		$this->addColumn('{{%admin}}', 'ip', 'varchar(20) NOT NULL COMMENT "登录ip"');
    }

    public function down()
    {
        echo "m160831_080324_update_admin_leoyan cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
