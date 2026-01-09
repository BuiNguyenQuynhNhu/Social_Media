<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        $userModel = model('UserModel'); 
        
        for ($i = 0; $i < 10; $i++) {
            $userId = Uuid::uuid4()->toString();
            
            $userData = [
                'id'       => $userId,
                'email'    => $faker->email,
                'password' => password_hash('12345678', PASSWORD_BCRYPT), 
                'type'     => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('users')->insert($userData);

            $profileData = [
                'id'        => $userId, 
                'firstname' => $faker->firstName,
                'lastname'  => $faker->lastName,
                'gender'    => $faker->randomElement(['male', 'female', 'other']),
                'birth'     => $faker->date(),
                'background'=> 'default_bg.jpg',
            ];
            $this->db->table('profiles')->insert($profileData);
        }
    }
}