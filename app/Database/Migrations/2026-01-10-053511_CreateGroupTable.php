<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGroupTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'owner_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'avatar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'cover_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'privacy' =>[
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'post_requirement' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('owner_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('groups');
    }

    public function down()
    {
        $this->forge->dropTable('groups');
    }
}
