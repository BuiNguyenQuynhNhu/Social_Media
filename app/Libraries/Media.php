<?php

namespace App\Libraries;

use App\Libraries\Cloudinary;
use App\Libraries\MongoDB;

class Media
{
    protected $cloudinary;
    protected $mongo;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary();
        $this->mongo = new MongoDB();
    }

    public function uploadMultiple(array $files, string $folder): array
    {
        $mediaItems = [];
        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $res = $this->cloudinary->uploadPost($file->getTempName(), $folder);
                
                $mediaItems[] = [
                    'url'       => $res['secure_url'],
                    'public_id' => $res['public_id'],
                    'type'      => $res['resource_type'],
                    'width'     => $res['width'] ?? null,
                    'height'    => $res['height'] ?? null,
                    'format'    => $res['format']
                ];
            }
        }
        return $mediaItems;
    }

    public function storeMetadata(string $entityId, array $items, string $collectionName)
    {
        if (empty($items)) return;

        $collection = $this->mongo->getCollection($collectionName);
        return $collection->insertOne([
            'entity_id'  => $entityId,
            'items'      => $items,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function deleteMedia(string $entityId, string $collectionName)
    {
        $collection = $this->mongo->getCollection($collectionName);
        $mediaData = $collection->findOne(['entity_id' => $entityId]);

        if ($mediaData) {
            foreach ($mediaData['items'] as $item) {
                $this->cloudinary->destroy($item['public_id']);
            }
            $collection->deleteOne(['entity_id' => $entityId]);
        }
    }
}