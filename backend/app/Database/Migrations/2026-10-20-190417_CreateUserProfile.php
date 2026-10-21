<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserProfile extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => ['type' => 'SERIAL', 'null' => false, 'auto_increment' => true, 'unique' => true],
            'firstname' => ['type'=> 'VARCHAR', 'constraint'=>255, 'null'=>true, 'default'=> null],
            'lastname'=> ['type'=> 'VARCHAR', 'constraint'=> 255, 'null'=> true,'default'=> null],
            'gender'=> ['type'=> 'BOOLEAN', 'null'=>true, 'constraint'=> null],
            'birthday'=> ['type'=> 'DATETIME', 'null'=>true, 'default'=> null],
            'background'=>['type'=>'VARCHAR', 'null'=>true, 'default'=>null],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => false],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey(['user_id','pk_users']);
        $this->forge->addForeignKey('user_id','users','user_id','CASCADE','CASCADE');
        $this->forge->createTable('profiles');
    }

    public function down()
    {
        $this->forge->dropTable('profiles');
    }
}
