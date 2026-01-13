<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRequireTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved'],
                'default'    => 'pending',
            ],
            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('requires');
    }

    public function down()
    {
        $this->forge->dropTable('requires');
    }
}
