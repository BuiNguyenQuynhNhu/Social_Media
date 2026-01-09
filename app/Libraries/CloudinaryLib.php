<?php

namespace App\Libraries;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryLib
{
    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => 'dooswg5jx', 
                'api_key'    => '985351413442649', 
                'api_secret' => 'w-A4FY9JtIOOEz2r3yOnh5um14U'
            ],
            'url' => [
                'secure' => true 
            ]
        ]);
    }

    /**
     * Hàm upload ảnh lên Cloudinary
     * @param string $filePath Đường dẫn tạm thời của file (tmp_name)
     * @param string $folder Thư mục trên Cloud (ví dụ: 'social_media/posts')
     * @return string|null Trả về URL ảnh hoặc null nếu lỗi
     */
    public function uploadImage($filePath, $folder = 'social_app')
    {
        try {
            $upload = new UploadApi();
            $result = $upload->upload($filePath, [
                'folder' => $folder,
                'resource_type' => 'image',
                'format' => 'jpg', 
                'quality' => 'auto', 
                'fetch_format' => 'auto'
            ]);

            return $result['secure_url']; 
        } catch (\Exception $e) {
            log_message('error', 'Cloudinary Upload Error: ' . $e->getMessage());
            return null;
        }
    }
}