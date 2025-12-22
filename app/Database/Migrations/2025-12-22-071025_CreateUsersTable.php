<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use PHPUnit\Framework\Constraint\Constraint;
use Ramsey\Uuid;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'admin' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'picture' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'default' => 'other'
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'birth' => [
                'type' => 'DATE',
            ],

            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp'
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('email', false, true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
