<?php

namespace App\Infrastructure\External\Cloudinary;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_FORMATS = ['jpeg', 'jpg', 'png', 'webp', 'avif'];
    private const MAX_WIDTH = 2000;
    private const QUALITY = 85;
    private const FOLDER = 'interdiscount/products';

    public function upload(UploadedFile $file, string $folder = '', array $options = []): array
    {
        $this->validateFile($file);

        $uploadFolder = $folder ?: self::FOLDER;

        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $uploadFolder,
            'transformation' => [
                'width' => $options['width'] ?? self::MAX_WIDTH,
                'crop' => 'limit',
                'quality' => $options['quality'] ?? self::QUALITY,
                'fetch_format' => 'auto',
            ],
        ]);

        return [
            'public_id' => $result->getPublicId(),
            'url' => $result->getSecurePath(),
            'width' => $result->getWidth(),
            'height' => $result->getHeight(),
            'format' => $result->getExtension(),
            'size' => $result->getSize(),
        ];
    }

    public function delete(string $publicId): bool
    {
        try {
            $result = Cloudinary::destroy($publicId);
            return $result === 'ok';
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed', [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function generateUrl(string $publicId, int $width, int $height): string
    {
        return cloudinary()->getUrl($publicId, [
            'transformation' => [
                'width' => $width,
                'height' => $height,
                'crop' => 'fill',
                'quality' => self::QUALITY,
                'fetch_format' => 'auto',
            ],
        ]);
    }

    private function validateFile(UploadedFile $file): void
    {
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File size exceeds maximum of 5MB');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_FORMATS)) {
            throw new \InvalidArgumentException(
                'Invalid file format. Allowed: ' . implode(', ', self::ALLOWED_FORMATS)
            );
        }
    }
}
