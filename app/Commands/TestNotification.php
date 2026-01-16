<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\Notification;
use App\Models\User;

class TestNotification extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:notification';
    protected $description = 'Tests notification insertion';

    public function run(array $params)
    {
        CLI::write("Starting Notification Test...", 'yellow');

        $db = \Config\Database::connect();
        
        // 1. Get a valid user
        $user = $db->table('users')->limit(1)->get()->getRowArray();
        if (!$user) {
            CLI::error("No users found to test with.");
            return;
        }
        
        $userId = $user['id'];
        CLI::write("Using User ID: " . $userId);

        // 2. Try to create a notification
        try {
            CLI::write("Attempting insert...", 'yellow');
            $id = Notification::createNotification('test_type', $userId, $userId, ['foo' => 'bar']);
            
            if ($id) {
                CLI::write("SUCCESS! Notification created with ID: " . $id, 'green');
            } else {
                CLI::error("FAILURE. Create returned false.");
            }
            
        } catch (\Exception $e) {
            CLI::error("EXCEPTION HIT:");
            CLI::error($e->getMessage());
            CLI::error($e->getTraceAsString());
        }
    }
}
