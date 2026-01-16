<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixTriggers extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:fix-triggers';
    protected $description = 'Fixes bad triggers causing table errors';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // 1. Check for bad triggers
        $query = $db->query("SHOW TRIGGERS");
        $triggers = $query->getResultArray();
        $found = false;

        foreach ($triggers as $trigger) {
            $stmt = $trigger['Statement'];
            // Check for known typos in the trigger statement
            if (stripos($stmt, 'notificatioins') !== false || stripos($stmt, 'nontification') !== false) {
                 CLI::write("Found bad trigger: " . $trigger['Trigger'], 'red');
                 CLI::write("Statement: " . $stmt, 'yellow');
                 
                 try {
                     $db->query("DROP TRIGGER " . $trigger['Trigger']);
                     CLI::write("Dropped trigger successfully.", 'green');
                     $found = true;
                 } catch (\Exception $e) {
                     CLI::error("Failed to drop: " . $e->getMessage());
                 }
            }
        }

        if (!$found) {
            CLI::write("No bad triggers found containing 'notificatioins' or 'nontification'.", 'green');
        }
        
        // 2. Double check table existence (just in case)
        if (!$db->tableExists('notifications')) {
             CLI::error("Table 'notifications' MISSING!");
        } else {
             CLI::write("Table 'notifications' exists.", 'green');
        }
    }
}
