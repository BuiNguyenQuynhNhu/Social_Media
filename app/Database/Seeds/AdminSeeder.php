<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;
class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = array(
            'id' => Uuid::uuid4()->tostring(),
            'first_name' => 'first',
            'last_name' => 'last',
            'email' => 'admin_test@gmail.com',
            'password' => password_hash('12345678', PASSWORD_BCRYPT),
            'admin' => true,
            'gender' => 'male',
        );

        $this->db->table('users')->insert($data);
    }
}
