<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFriendTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'requester_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'receiver_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'nickname1' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'nickname2' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'accepted'],
                'default' => 'pending',
            ],

            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
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
