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
        $username = getenv('MONGO_USERNAME');
        $password = getenv('MONGO_PASSWORD');
        $host     = getenv('MONGO_HOST');
        $port     = getenv('MONGO_PORT');
        $dbname   = getenv('MONGO_DB');

        $uri = "mongodb://localhost:$port";
        $this->client = new Client($uri);
        $this->db = $this->client->selectDatabase($dbname);
        
    }

    public function getCollection($name)
    {
        return $this->db->selectCollection($name);
    }
}