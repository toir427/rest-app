<?php

use yii\db\Migration;

/**
 * Class m221001_101513_company
 */
class m221001_101513_company extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'leader_name' => $this->string(),
            'address' => $this->string(),
            'email' => $this->string()->notNull()->unique(),
            'website' => $this->string(),
            'phone_number' => $this->string(),
            'user_id' => $this->integer()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-employee-user_id',
            'company',
            'user_id',
            'user',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-employee-user_id', '{{%company}}');
        $this->dropTable('{{%company}}');
    }
}
