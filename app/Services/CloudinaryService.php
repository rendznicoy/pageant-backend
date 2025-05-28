<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }

    /**
     * Upload file to Cloudinary
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', array $options = []): ?array
    {
        try {
            Log::info('CloudinaryService upload started', [
                'folder' => $folder,
                'filename' => $file->getClientOriginalName()
            ]);

            $uploadOptions = array_merge([
                'folder' => $folder,
                'resource_type' => 'auto',
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ], $options);

            $result = $this->cloudinary->uploadApi()->upload($file->getPathname(), $uploadOptions);
            
            Log::info('Cloudinary upload successful', [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url']
            ]);
            
            return [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'width' => $result['width'] ?? null,
                'height' => $result['height'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Delete file from Cloudinary
     */
    public function delete(string $publicId): bool
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate optimized URL
     */
    public function url(string $publicId, array $transformations = []): string
    {
        return $this->cloudinary->image($publicId)
            ->resize(Resize::fill()->width($transformations['width'] ?? 800)->height($transformations['height'] ?? 600))
            ->toUrl();
    }
}