<?php

use yii\db\Migration;

class m160831_075450_admin extends Migration
{

	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			// http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%admin}}', [
			'id' => $this->primaryKey(),
			'admin' => $this->string()->notNull()->unique(),
			'password' => $this->string()->notNull(),
			'email' => $this->string()->notNull()->unique(),
			'phone' => $this->string(11),
			'status' => $this->smallInteger()->notNull()->defaultValue(10)
		], $tableOptions);
	}

	public function down()
	{
		$this->dropTable('{{%admin}}');

        echo "m160831_075450_admin cannot be reverted.\n";

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
