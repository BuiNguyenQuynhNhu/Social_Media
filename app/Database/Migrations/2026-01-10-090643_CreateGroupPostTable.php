<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGroupPostTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'group_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'post_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'accepted', 'reject'],
                'default' => 'pending',
            ],
            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('group_id', 'groups', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('group_posts');
    }

    public function down()
    {
        $this->forge->dropTable('group_posts');
    }
}
