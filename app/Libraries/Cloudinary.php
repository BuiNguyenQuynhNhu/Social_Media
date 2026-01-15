<?php

namespace App\Libraries;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class Cloudinary
{
    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => getenv('CLOUDINARY_API_KEY'),
                'api_secret' => getenv('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true]
        ]);
    }

    /**
     * Uploads a file to Cloudinary
     * @param string $file The path to the temporary file
     * @param string $folder The destination folder in Cloudinary
     */
    public function upload($file, $folder){
        $upload = new UploadApi();
        $response = $upload->upload($file, [
            'folder' => 'social_media/' . $folder,
            'resource_type' => 'auto'
        ]);
        return $response['secure_url']; // Returns the permanent HTTPS URL
    }

    public function destroy($publicId){
        $upload = new UploadApi();
        return $upload->destroy($publicId);
    }

    public function uploadPost($file, $folder){
        $upload = new UploadApi();
        $response = $upload->upload($file, [
            'folder' => 'social_media/' . $folder,
            'resource_type' => 'auto'
        ]);
        return $response;
    }
}