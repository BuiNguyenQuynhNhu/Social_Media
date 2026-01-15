<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'content' => [
                'type' => 'TEXT',
            ],
            'id_user' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'privacy' => [
                'type' => 'ENUM',
                'constraint' => ['public', 'private'],
                'default' => 'public',
            ],

            'created_at timestamp default current_timestamp',
            
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_user', 'users', 'id', 'cascade', 'cascade');
        $this->forge->createTable('posts');
    }

    public function down()
    {
        $this->forge->dropTable('posts');
    }
}
