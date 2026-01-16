<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $userModel = new \App\Models\User();
        $users = $userModel->findAll();
        $userIds = array_column($users, 'id');
        
        $postModel = new \App\Models\Post();
        $posts = $postModel->findAll(50); // Get latest 50 posts
        $postIds = array_column($posts, 'id');

        if (count($userIds) < 2) return;

        $notificationModel = new \App\Models\Notification();

        for ($i = 0; $i < 15; $i++) {
            $owner = $userIds[array_rand($userIds)];
            
            // Actor must be different from Owner
            do {
                $actor = $userIds[array_rand($userIds)];
            } while ($actor === $owner);

            $type = ($i % 3 == 0) ? 'post_like' : (($i % 3 == 1) ? 'friend_request' : 'comment_add');
            
            $context = [];
            if (($type == 'post_like' || $type == 'comment_add') && !empty($postIds)) {
                $postId = $postIds[array_rand($postIds)];
                $context['post_id'] = $postId;
                $context['preview'] = ($type == 'comment_add') ? 'Nice post!' : '';
            }

            \App\Models\Notification::createNotification($type, $actor, $owner, $context);
        }
    }
}