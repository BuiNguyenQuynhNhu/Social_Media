<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FriendshipSeeder extends Seeder
{
    public function run()
    {
        $userIds = $this->db->table('users')->select('id')->get()->getResultArray();
        
        if (count($userIds) < 2) return;

        for ($i = 0; $i < 10; $i++) {
            // Chọn 2 user khác nhau
            $key1 = array_rand($userIds);
            do {
                $key2 = array_rand($userIds);
            } while ($key1 === $key2);

            $requester = $userIds[$key1]['id'];
            $receiver  = $userIds[$key2]['id'];

            // Kiểm tra trùng lặp (đơn giản hóa cho seeder)
            $exists = $this->db->table('friendships')
                ->where('requester_id', $requester)
                ->where('receiver_id', $receiver)
                ->countAllResults();

            if ($exists == 0) {
                $this->db->table('friendships')->insert([
                    'requester_id' => $requester,
                    'receiver_id'  => $receiver,
                    'status'       => 'accepted', // Hoặc 'pending'
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}