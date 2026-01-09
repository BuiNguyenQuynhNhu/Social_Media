<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $userIds = $this->db->table('users')->select('id')->get()->getResultArray();
        
        // Lấy danh sách ID của post và comment để làm reference_id
        $postIds = $this->db->table('posts')->select('id')->get()->getResultArray();
        
        if (count($userIds) < 2) return;

        for ($i = 0; $i < 10; $i++) {
            $owner = $userIds[array_rand($userIds)]['id'];
            
            // Actor phải khác Owner
            do {
                $actor = $userIds[array_rand($userIds)]['id'];
            } while ($actor === $owner);

            $type = ($i % 2 == 0) ? 'post_like' : 'friend_request';
            
            $refId = null;
            if ($type == 'post_like' && !empty($postIds)) {
                $refId = $postIds[array_rand($postIds)]['id'];
            }

            $this->db->table('notifications')->insert([
                'user_id'      => $owner,
                'actor_id'     => $actor,
                'type'         => $type,
                'reference_id' => $refId, // ID bài viết hoặc NULL tùy loại
                'is_read'      => 0,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }
    }
}