<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class TestUser extends Seeder
{
    public function run()
    {
        $user = new User();
        $profile = new Profile();

        $account = [
            'username' => env('seeder.user.username'),
            'email'=> env('seeder.user.email'),
            'password' => password_hash(env('seeder.user.password'), PASSWORD_DEFAULT),
        ];

        $user->insert($account);
        $userID = $user->getInsertID();
        $info = [
            'firstname' => env('seeder.user.firstname'),
            'lastname' => env('seeder.user.lastname'),
            'birthday' => env('seeder.user.birthday'),
            'user_id' => $userID,
        ];
        $profile->insert($info);
    }
}
