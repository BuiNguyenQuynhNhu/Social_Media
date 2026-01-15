<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatMemberTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'chat_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'role' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'nickname' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'joined_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('chat_id', 'chats', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('chat_members');
    }

    public function down()
    {
        //
    }
}
