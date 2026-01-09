<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFriendshipsTable extends Migration
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
            'requester_id' => [ 
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'receiver_id' => [ 
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'accepted', 'blocked'],
                'default'    => 'pending',
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

        $this->forge->addKey('id', true);
        
        $this->forge->addForeignKey('requester_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('receiver_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('friendships');
    }

    public function down()
    {
        $this->forge->dropTable('friendships');
    }
}
