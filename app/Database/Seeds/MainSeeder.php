<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('PostSeeder');
        $this->call('FriendshipSeeder');
        $this->call('InteractionSeeder');
        $this->call('NotificationSeeder');
    }
}