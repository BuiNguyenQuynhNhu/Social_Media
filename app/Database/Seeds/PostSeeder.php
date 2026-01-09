<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        
        // Lấy tất cả user id hiện có
        $userIds = $this->db->table('users')->select('id')->get()->getResultArray();
        
        if (empty($userIds)) return;

        // Tạo 10-15 bài viết
        for ($i = 0; $i < 15; $i++) {
            $randomUser = $userIds[array_rand($userIds)]['id'];
            
            $data = [
                'user_id'    => $randomUser,
                'content'    => $faker->paragraph,
                'media_url'  => $faker->imageUrl(640, 480, 'cats'),
                'privacy'    => $faker->randomElement(['public', 'friends', 'private']),
                'created_at' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->table('posts')->insert($data);
        }
    }
}