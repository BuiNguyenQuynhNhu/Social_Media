<?php

namespace App\Libraries;

use MongoDB\Client;
use Exception;

class MongoDB
{
    protected $client;
    protected $db;

    public function __construct()
    {
        try {
            $username = getenv('MONGO_USERNAME');
            $password = getenv('MONGO_PASSWORD');
            $host     = getenv('MONGO_HOST') ?: 'localhost';
            $port     = getenv('MONGO_PORT') ?: '27017';
            $dbname   = getenv('MONGO_DB') ?: 'social_media';

            $uri = "mongodb://$host:$port";
            $this->client = new Client($uri);
            $this->db = $this->client->selectDatabase($dbname);
        } catch (Exception $e) {
            log_message('error', 'MongoDB connection failed: ' . $e->getMessage());
            throw new Exception('Failed to connect to MongoDB: ' . $e->getMessage());
        }
        
    }

    public function getCollection($name)
    {
        return $this->db->selectCollection($name);
    }
}