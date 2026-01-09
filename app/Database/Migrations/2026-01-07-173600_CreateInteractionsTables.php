<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInteractionsTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
    'id' => [
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => true,
        'auto_increment' => true
    ],
    'user_id' => [
        'type' => 'CHAR',
        'constraint' => 36
    ],
    'post_id' => [
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => true
    ],
    'reaction' => [
        'type' => 'ENUM',
        'constraint' => [
            'like',
            'love',
            'haha',
            'wow',
            'sad',
            'angry'
        ],
        'default' => 'like'
    ],
    'created_at' => [
        'type' => 'DATETIME',
        'null' => true
    ],
    'updated_at' => [
        'type' => 'DATETIME',
        'null' => true
    ],
]);

$this->forge->addKey('id', true);
$this->forge->addUniqueKey(['user_id', 'post_id']);
$this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
$this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');

$this->forge->createTable('reactions');

$this->forge->addField([
    'id' => [
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => true,
        'auto_increment' => true
    ],

    'user_id' => [
        'type' => 'CHAR',
        'constraint' => 36
    ],

    'post_id' => [
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => true
    ],

    'parent_id' => [
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => true,
        'null' => true
    ],

    'content' => [
        'type' => 'TEXT'
    ],

    'created_at' => [
        'type' => 'DATETIME',
        'null' => true
    ],

    'updated_at' => [
        'type' => 'DATETIME',
        'null' => true
    ],

    'deleted_at' => [
        'type' => 'DATETIME',
        'null' => true
    ],
]);

$this->forge->addKey('id', true);
$this->forge->addKey('post_id');        
$this->forge->addKey('parent_id');      

$this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
$this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
$this->forge->addForeignKey('parent_id', 'comments', 'id', 'CASCADE', 'CASCADE');

$this->forge->createTable('comments');


}

    public function down()
    {
        $this->forge->dropTable('comments');
        $this->forge->dropTable('reactions');
    }
}
