<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InteractionSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        $userIds = $this->db->table('users')->select('id')->get()->getResultArray();
        $postIds = $this->db->table('posts')->select('id')->get()->getResultArray();

        if (empty($userIds) || empty($postIds)) return;

        // 1. Tạo Reactions (Like, Love...)
        for ($i = 0; $i < 20; $i++) {
            $uId = $userIds[array_rand($userIds)]['id'];
            $pId = $postIds[array_rand($postIds)]['id'];

            // Tránh duplicate key (user_id, post_id)
            $exists = $this->db->table('reactions')
                ->where('user_id', $uId)
                ->where('post_id', $pId)
                ->countAllResults();

            if ($exists == 0) {
                $this->db->table('reactions')->insert([
                    'user_id'    => $uId,
                    'post_id'    => $pId,
                    'reaction'   => $faker->randomElement(['like', 'love', 'haha', 'wow', 'sad', 'angry']),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 2. Tạo Comments
        for ($i = 0; $i < 15; $i++) {
            $uId = $userIds[array_rand($userIds)]['id'];
            $pId = $postIds[array_rand($postIds)]['id'];

            $this->db->table('comments')->insert([
                'user_id'    => $uId,
                'post_id'    => $pId,
                'content'    => $faker->sentence,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}