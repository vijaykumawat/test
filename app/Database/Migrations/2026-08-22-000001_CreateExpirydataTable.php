<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExpirydataTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'regNumber' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'expiryDate' => [
                'type'       => 'DATE',
                'null'       => true,
                'default'    => null,
            ],
            'employeeId' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('expirydata');
    }

    public function down()
    {
        $this->forge->dropTable('expirydata');
    }
}