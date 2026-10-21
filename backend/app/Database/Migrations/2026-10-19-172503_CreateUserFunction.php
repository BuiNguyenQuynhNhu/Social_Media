<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserFunction extends Migration
{
    public function up()
    {
        $this->db->query('CREATE FUNCTION get_user_mail(p_mail VARCHAR) RETURNS TABLE(user_id INTEGER, avatar VARCHAR, password VARCHAR, role VARCHAR, username VARCHAR)
        LANGUAGE plpgsql AS $$
            BEGIN
                RETURN QUERY 
                    SELECT u.user_id, u.avatar, u.password, u.role, u.username
                    FROM users AS u WHERE u.email = p_mail;
            END;

        $$');
        
        $this->db->query('CREATE FUNCTION update_user_password(n_password VARCHAR, p_id INTEGER) RETURNS void
        LANGUAGE plpgsql AS $$
            BEGIN
                UPDATE users SET password = n_password WHERE user_id = p_id;
            END;
        $$');

        $this->db->query('CREATE FUNCTION update_user_avatar(p_id INTEGER, p_avatar VARCHAR) RETURNS void
        LANGUAGE plpgsql as $$
            BEGIN
                UPDATE users SET avatar = p_avatar WHERE user_id = p_id;
            END;
        $$');

        $this->db->query('CREATE FUNCTION update_user_username(p_id INTEGER, p_username VARCHAR) RETURNS void
        LANGUAGE plpgsql AS $$
            BEGIN
                UPDATE users SET username = p_username WHERE user_id = p_id;
            END;
        $$');
    }

    public function down()
    {
        $this->db->query('DROP FUNCTION IF EXISTS get_user_mail(VARCHAR);');
        $this->db->query('DROP FUNCTION IF EXISTS update_user_password(VARCHAR, INTEGER);');
        $this->db->query('DROP FUNCTION IF EXISTS update_user_avatar(INTEGER, VARCHAR);');
        $this->db->query('DROP FUNCTION IF EXISTS update_user_username(INTEGER, VARCHAR);');
    }
}
