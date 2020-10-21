<?php
namespace mix8872\menu\migrations;

use yii\db\Migration;

class m170803_125415_create_menu_table extends Migration
{
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName == 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%menu}}', [
			'id' => $this->primaryKey(),
			'lft' => $this->integer()->notNull(),
			'rgt' => $this->integer()->notNull(),
			'depth' => $this->integer()->notNull(),
			'tree' => $this->integer()->defaultValue(null),
			'type' => $this->tinyInteger(1)->defaultValue(0),
			'name' => $this->string(255)->notNull(),
			'code' => $this->string(255)->unique(),
			'url' => $this->string(255),
			'icon_class' => $this->string(255),
			'description' => $this->string(255)->defaultValue(''),
            'modelClass' => $this->string(255),
            'codeAttr' => $this->string(255),
            'titleAttr' => $this->string(255),
            'descriptionAttr' => $this->string(255),
            'requestOptions' => $this->string(255)
		], $tableOptions);
    }

    public function safeDown()
    {
       $this->dropTable('{{%menu}}');
    }
}
