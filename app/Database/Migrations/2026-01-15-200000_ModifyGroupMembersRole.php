<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyGroupMembersRole extends Migration
{
    public function up()
    {
        $fields = [
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'member',
            ],
        ];
        $this->forge->modifyColumn('group_members', $fields);
    }

    public function down()
    {
        $fields = [
            'role' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
        ];
        $this->forge->modifyColumn('group_members', $fields);
    }
}
