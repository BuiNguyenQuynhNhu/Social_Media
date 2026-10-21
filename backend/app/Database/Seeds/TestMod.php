<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class TestMod extends Seeder
{
    public function run()
    {
        $user = new User();
        $profile = new Profile();

        $account = [
            'username' => env('seeder.mod.username'),
            'email'=> env('seeder.mod.email'),
            'password' => password_hash(env('seeder.mod.password'), PASSWORD_DEFAULT),
            'role' => 2,
        ];


        $user->insert($account);
        $userID = $user->getInsertID();
        $info = [
            'firstname' => env('seeder.mod.firstname'),
            'lastname' => env('seeder.mod.lastname'),
            'birthday' => env('seeder.mod.birthday'),
            'user_id' => $userID,
        ];
        $profile->insert($info);
    }
}
