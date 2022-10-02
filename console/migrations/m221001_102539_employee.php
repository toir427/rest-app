<?php

use yii\db\Migration;

/**
 * Class m221001_102539_employee
 */
class m221001_102539_employee extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%employee}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(),
            'last_name' => $this->string(),
            'surname' => $this->string(),
            'passport' => $this->string(),
            'position' => $this->smallInteger()->notNull()->defaultValue(10),
            'phone_number' => $this->string(),
            'address' => $this->string(),
            'company_id' => $this->integer()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-employee-company_id',
            'employee',
            'company_id',
            'company',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-employee-company_id', '{{%employee}}');
        $this->dropTable('{{%employee}}');
    }
}