<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NontificationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],

            'context' => [
                'type' => 'TEXT',
            ],

            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'responsed', 'approved', 'updated', 'done', 'cancelled'],
            ],
            'requester_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'receiver_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id', true);
        
        $this->forge->addForeignKey('requester_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('receiver_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('nontifications');
    }

    public function down()
    {
        $this->forge->dropTable('nontifications');
    }
}
