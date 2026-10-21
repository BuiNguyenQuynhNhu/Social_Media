<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixUserDeleteAt extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE users ALTER COLUMN deleted_at DROP NOT NULL;");
    }

    public function down()
    {
        //
    }
}
