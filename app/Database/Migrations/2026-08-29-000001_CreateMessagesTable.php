<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'messageId' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'senderId' => [
                'type'       => 'CHAR',
                'constraint' => 16,
                'null'       => false,
            ],
            'receiverId' => [
                'type'       => 'CHAR',
                'constraint' => 16,
                'null'       => false,
            ],
            'messageText' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'createdAt' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('messageId', true);
        $this->forge->addKey(['senderId', 'receiverId']);
        $this->forge->addKey('senderId');
        $this->forge->addKey('receiverId');
        $this->forge->addKey('createdAt');

        // Foreign keys to the existing employee table
        $this->forge->addForeignKey('senderId', 'employee', 'employeeId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('receiverId', 'employee', 'employeeId', 'CASCADE', 'CASCADE');

        // ifNotExists => safe when the table was already created manually
        // (e.g. via CHAT_SYSTEM_SETUP.sql in phpMyAdmin)
        $this->forge->createTable('messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('messages');
    }
}