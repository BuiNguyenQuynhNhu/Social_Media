<?php

namespace App\Models;

use CodeIgniter\Model;

class ReactionModel extends Model
{
    protected $table            = 'reactions';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'post_id', 'reaction'];
    protected $useTimestamps    = true; 
}