<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type'       => 'INT',
                'null'       => false,
            ],
            'attendance_date' => [
                'type'       => 'DATE',
                'null'       => false,
            ],
            'check_in_time' => [
                'type'       => 'TIME',
                'null'       => true,
            ],
            'check_out_time' => [
                'type'       => 'TIME',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Present', 'Absent', 'Half Day', 'Leave'],
                'default'    => 'Present',
            ],
            'remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'default'    => '2024-01-01 00:00:00',
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['employee_id', 'attendance_date']);
        $this->forge->createTable('attendance');
    }

    public function down()
    {
        $this->forge->dropTable('attendance');
    }
}
