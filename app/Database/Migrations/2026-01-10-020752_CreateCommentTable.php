<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommentTable extends Migration
{
public function up()
{
    $this->forge->addField([
        'id' => [
            'type'       => 'CHAR',
            'constraint' => 36,
        ],
        'post_id' => [
            'type'       => 'CHAR',
            'constraint' => 36,
        ],
        'user_id' => [
            'type'       => 'CHAR',
            'constraint' => 36,
        ],
        'parent_id' => [
            'type'       => 'CHAR',
            'constraint' => 36,
            'null'       => true,
        ],
        'content' => [
            'type' => 'TEXT',
        ],
        'created_at timestamp default current_timestamp',
    ]);
    
    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('parent_id', 'comments', 'id', 'CASCADE');

    $this->forge->createTable('comments');
}

    public function down()
    {
        $this->forge->dropTable('interations');
    }
}
