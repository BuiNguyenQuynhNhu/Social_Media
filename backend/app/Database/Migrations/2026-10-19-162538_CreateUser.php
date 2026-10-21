<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUser extends Migration
{
    public function up(){
        $this->forge->addField([
            'user_id' => ['type' => 'SERIAL', 'null' => false, 'auto_increment' => true, 'unique' => true],
            'username' => ['type' => 'VARCHAR', 'null'=> false,'constraint'=> 32],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'avatar' => ['type'=> 'VARCHAR','constraint'=> 11,'null'=> true],
            'role' => ['type' => 'INT','default'=> 3,'null'=> false],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => false],
            'deleted_at' => ['type' => 'DATETIME', 'null' => false]
        ]);
        $this->forge->addPrimaryKey('user_id', 'pk_users');
        $this->forge->createTable("users");
    }

    public function down(){
        $this->forge->dropTable("users");
    }
}
