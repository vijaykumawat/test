<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePoliciesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'policy_number' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => true,
            ],
            'holder_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'vehicle_number' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'insurance_type' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'issue_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', false, true);
        $this->forge->addKey('policy_number');
        $this->forge->addKey('expiry_date');
        $this->forge->createTable('policies');
    }

    public function down()
    {
        $this->forge->dropTable('policies');
    }
}
