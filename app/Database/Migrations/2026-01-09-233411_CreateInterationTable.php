<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'post_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['like','love','haha','wow','sad','angry'],
                'default' => 'like',
            ],
            

            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id', true);
        
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('interations');
    }

    public function down()
    {
        $this->forge->dropTable('interations');
    }
}
