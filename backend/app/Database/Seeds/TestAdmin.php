<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\User;

class TestAdmin extends Seeder
{
    public function run()
    {
        $user = new User();

        $account = [
            'username' => env('seeder.admin.username'),
            'email'=> env('seeder.admin.email'),
            'password' => password_hash(env('seeder.admin.password'), PASSWORD_DEFAULT),
            'role' => 1,
        ];


        $user->insert($account);
    }
}
