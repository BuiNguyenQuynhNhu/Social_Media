<?php

/**
 * Database Seeder for Test Users
 * Run this file once to populate your database with dummy users for testing
 * 
 * Usage: php spark db:seed TestUserSeeder
 */

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Dummy user data (realistic names and info)
        $users = [
            [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'john.doe@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'gender' => 'Male',
                'birth' => '1995-03-15',
                'avatar' => 'https://i.pravatar.cc/150?img=12'
            ],
            [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'email' => 'jane.smith@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'gender' => 'Female',
                'birth' => '1998-07-22',
                'avatar' => 'https://i.pravatar.cc/150?img=5'
            ],
            [
                'firstname' => 'Michael',
                'lastname' => 'Johnson',
                'email' => 'michael.j@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'gender' => 'Male',
                'birth' => '1992-11-08',
                'avatar' => 'https://i.pravatar.cc/150?img=33'
            ],
            [
                'firstname' => 'Emily',
                'lastname' => 'Davis',
                'email' => 'emily.davis@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'gender' => 'Female',
                'birth' => '1996-05-30',
                'avatar' => 'https://i.pravatar.cc/150?img=9'
            ],
            [
                'firstname' => 'David',
                'lastname' => 'Wilson',
                'email' => 'david.wilson@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'gender' => 'Male',
                'birth' => '1994-01-19',
                'avatar' => 'https://i.pravatar.cc/150?img=15'
            ],
            [
                'firstname' => 'Sarah',
                'lastname' => 'Taylor',
                'email' => 'sarah.taylor@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'gender' => 'Female',
                'birth' => '1997-09-12',
                'avatar' => 'https://i.pravatar.cc/150?img=20'
            ],
            [
                'firstname' => 'James',
                'lastname' => 'Brown',
                'email' => 'james.brown@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'gender' => 'Male',
                'birth' => '1993-04-27',
                'avatar' => 'https://i.pravatar.cc/150?img=11'
            ],
            [
                'firstname' => 'Lisa',
                'lastname' => 'Anderson',
                'email' => 'lisa.anderson@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'gender' => 'Female',
                'birth' => '1999-12-05',
                'avatar' => 'https://i.pravatar.cc/150?img=23'
            ]
        ];

        echo "Creating dummy users...\n";
        
        $userIds = [];
        foreach ($users as $userData) {
            // Generate UUID for user
            $userId = \Ramsey\Uuid\Uuid::uuid4()->toString();
            
            // Insert into users table
            $db->table('users')->insert([
                'id' => $userId,
                'email' => $userData['email'],
                'password' => $userData['password'],
                'avatar' => $userData['avatar'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Insert into profiles table
            $db->table('profiles')->insert([
                'id' => $userId,
                'firstname' => $userData['firstname'],
                'lastname' => $userData['lastname'],
                'gender' => $userData['gender'],
                'birth' => $userData['birth'],
                'background' => 'https://picsum.photos/1200/400?random=' . rand(1, 100)
            ]);
            
            $userIds[] = $userId;
            echo "✓ Created: {$userData['firstname']} {$userData['lastname']} ({$userData['email']})\n";
        }

        // Create some friendships for testing
        echo "\nCreating friendships...\n";
        $friendships = [
            [$userIds[0], $userIds[1]], // John & Jane
            [$userIds[0], $userIds[2]], // John & Michael
            [$userIds[1], $userIds[3]], // Jane & Emily
            [$userIds[2], $userIds[4]], // Michael & David
            [$userIds[3], $userIds[5]], // Emily & Sarah
        ];

        foreach ($friendships as $friendship) {
            $db->table('friendships')->insert([
                'requester_id' => $friendship[0],
                'receiver_id' => $friendship[1],
                'status' => 'accepted',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            echo "✓ Created friendship\n";
        }

        echo "\n✅ Done! Created " . count($users) . " users with " . count($friendships) . " friendships.\n";
        echo "📧 All accounts use password: password123\n";
    }
}
