<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;
class UserSeeder extends Seeder
{
    public function run()
    {
        $userId = Uuid::uuid4()->toString();
        $user = array(
            'id' => $userId,
            'email' => 'test@gmail',
            'password' => password_hash('12345678', PASSWORD_BCRYPT),
        );
        $this->db->table('users')->insert($user);

        $profile = array(
            'id' => $userId,
            'firstname' => 'test',
            'lastname' => '1',
            'birth' => '1990-01-01',
        );
        $this->db->table('profiles')->insert($profile);
    }
}
