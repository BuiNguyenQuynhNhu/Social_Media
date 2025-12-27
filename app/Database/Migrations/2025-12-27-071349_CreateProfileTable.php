<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProfileTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'default' => 'other',
            ],
            'birth' => [
                'type' => 'DATE',
            ],
            'firstname' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'background' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],

            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id', 'users', 'id', 'cascade', 'cascade');
        $this->forge->createTable('profiles');
    }

    public function down()
    {
        $this->forge->dropTable('profiles');
    }
}
