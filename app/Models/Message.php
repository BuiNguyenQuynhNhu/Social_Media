<?php

namespace App\Models;

use App\Libraries\MongoDB;
use MongoDB\BSON\ObjectId;

class Message {
    protected $collection;

    public function __construct() {
        $mongo = new MongoDB();
        $this->collection = $mongo->getCollection('messages');
    }

    public function save($data) {
        return $this->collection->insertOne($data);
    }

    public function toggle($messageId, $userId, $type) {
        $oid = new ObjectId($messageId);
        $message = $this->collection->findOne(['_id' => $oid]);
        if (!$message) return false;

        $reactions = (array)($message['reactions'] ?? []);
        $foundIndex = -1;
        foreach ($reactions as $index => $res) {
            if ($res['user_id'] === $userId) {
                $foundIndex = $index;
                break;
            }
        }

        if ($foundIndex !== -1) {
            if ($reactions[$foundIndex]['type'] === $type) {
                array_splice($reactions, $foundIndex, 1);
            } else {
                $reactions[$foundIndex]['type'] = $type;
            }
        } else {
            $reactions[] = ['user_id' => $userId, 'type' => $type];
        }

        return $this->collection->updateOne(['_id' => $oid], ['$set' => ['reactions' => $reactions]]);
    }

    public function get($userId, $receiverId) {
        return $this->collection->find([
            '$or' => [
                ['sender_id' => $userId, 'receiver_id' => $receiverId],
                ['sender_id' => $receiverId, 'receiver_id' => $userId],
                ['receiver_id' => $receiverId, 'chat_type' => 'group']
            ]
        ], ['sort' => ['created_at' => 1]])->toArray();
    }
}