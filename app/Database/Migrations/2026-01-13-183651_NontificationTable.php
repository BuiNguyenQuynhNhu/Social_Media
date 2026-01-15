<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NontificationTable extends Migration
{
    public function up()
    {
        // Drop old table if exists (from previous migration or incorrect schema)
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('nontifications', true);

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
                'constraint' => ['unread', 'read', 'pending', 'responsed', 'approved', 'updated', 'done', 'cancelled'],
                'default' => 'unread'
            ],
            'requester_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'receiver_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id', true);
        
        $this->forge->addForeignKey('requester_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('receiver_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}
