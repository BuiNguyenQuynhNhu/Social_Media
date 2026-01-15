<?php

/**
 * CSV-Based Database Seeder for Facebook Test Users
 * Uses data from tests/database/facebook.csv
 * 
 * Usage: php spark db:seed FacebookCsvSeeder
 */

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FacebookCsvSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $csvPath = ROOTPATH . 'tests/database/facebook.csv';

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file); // Skip header row
        
        $userCount = 0;
        $maxUsers = 20; // Limit to 20 users for testing (change as needed)
        
        $firstNames = ['John', 'Jane', 'Michael', 'Emily', 'David', 'Sarah', 'James', 'Lisa', 'Robert', 'Maria', 
                      'William', 'Jennifer', 'Richard', 'Linda', 'Thomas', 'Patricia', 'Charles', 'Barbara'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 
                     'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor'];
        
        $userIds = [];
        
        while (($row = fgetcsv($file)) !== FALSE && $userCount < $maxUsers) {
            // Parse CSV: userid,age,dob_day,dob_year,dob_month,gender,tenure,friend_count...
            $userid = $row[0];
            $age = $row[1];
            $dobDay = $row[2];
            $dobYear = $row[3];
            $dobMonth = $row[4];
            $gender = ucfirst($row[5]); // male/female -> Male/Female
            $friendCount = $row[7] ?? 0;
            
            // Generate random name
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $email = strtolower($firstName . '.' . $lastName . $userid . '@test.com');
            
            // Create birth date
            $birthDate = sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay);
            
            // Generate UUID
            $userId = \Ramsey\Uuid\Uuid::uuid4()->toString();
            
            // Random avatar from pravatar
            $avatarNum = ($userCount % 70) + 1;
            $avatar = "https://i.pravatar.cc/150?img={$avatarNum}";
            
            try {
                // Insert user
                $db->table('users')->insert([
                    'id' => $userId,
                    'email' => $email,
                    'password' => password_hash('password123', PASSWORD_DEFAULT),
                    'avatar' => $avatar,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                // Insert profile
                $db->table('profiles')->insert([
                    'id' => $userId,
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                    'gender' => $gender,
                    'birth' => $birthDate,
                    'background' => 'https://picsum.photos/1200/400?random=' . $userCount
                ]);
                
                $userIds[] = $userId;
                $userCount++;
                
                echo "✓ Created: {$firstName} {$lastName} (Age: {$age}, Friends: {$friendCount})\n";
                
            } catch (\Exception $e) {
                echo "⚠️  Skipped user {$email}: {$e->getMessage()}\n";
                continue;
            }
        }
        
        fclose($file);
        
        // Create some random friendships
        echo "\n👥 Creating friendships...\n";
        $friendshipCount = 0;
        $maxFriendships = min(15, count($userIds) - 1);
        
        for ($i = 0; $i < $maxFriendships; $i++) {
            $user1 = $userIds[array_rand($userIds)];
            $user2 = $userIds[array_rand($userIds)];
            
            if ($user1 === $user2) continue;
            
            try {
                $db->table('friendships')->insert([
                    'requester_id' => $user1,
                    'receiver_id' => $user2,
                    'status' => 'accepted',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $friendshipCount++;
                echo "✓ Created friendship #{$friendshipCount}\n";
            } catch (\Exception $e) {
                // Skip duplicate friendships
                continue;
            }
        }
        
        echo "\n✅ Seeding complete!\n";
        echo "👤 Created {$userCount} users\n";
        echo "👥 Created {$friendshipCount} friendships\n";
        echo "🔑 All accounts use password: password123\n";
    }
}
