<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsReadToMessages extends Migration
{
    public function up()
    {
        // Unread tracking: 0 = not yet seen by the receiver, 1 = seen.
        $this->forge->addColumn('messages', [
            'isRead' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'messageText',
                'comment'    => '0 = unread, 1 = read by the receiver',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('messages', 'isRead');
    }
}