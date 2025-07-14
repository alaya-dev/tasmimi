<?php

namespace App\Services;

use App\Models\UserFile;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PexelsService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.pexels.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.pexels.api_key', env('PEXELS_API_KEY'));
    }

    /**
     * Search for photos on Pexels.
     */
    public function searchPhotos(string $query, int $page = 1, int $perPage = 20): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'مفتاح API الخاص بـ Pexels غير مُعرَّف'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/search", [
                'query' => $query,
                'page' => $page,
                'per_page' => $perPage,
                'orientation' => 'all',
                'size' => 'large'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'photos' => $this->formatPhotos($data['photos'] ?? []),
                    'total_results' => $data['total_results'] ?? 0,
                    'page' => $data['page'] ?? 1,
                    'per_page' => $data['per_page'] ?? $perPage,
                    'next_page' => $data['next_page'] ?? null
                ];
            }

            return [
                'success' => false,
                'message' => 'فشل في البحث عن الصور: ' . $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في الاتصال بـ Pexels: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get curated photos from Pexels.
     */
    public function getCuratedPhotos(int $page = 1, int $perPage = 20): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'مفتاح API الخاص بـ Pexels غير مُعرَّف'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/curated", [
                'page' => $page,
                'per_page' => $perPage
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'photos' => $this->formatPhotos($data['photos'] ?? []),
                    'page' => $data['page'] ?? 1,
                    'per_page' => $data['per_page'] ?? $perPage,
                    'next_page' => $data['next_page'] ?? null
                ];
            }

            return [
                'success' => false,
                'message' => 'فشل في جلب الصور المختارة: ' . $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في الاتصال بـ Pexels: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Download and save a photo from Pexels.
     */
    public function downloadPhoto(int $photoId, string $size = 'large', ?string $folder = 'pexels'): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'مفتاح API الخاص بـ Pexels غير مُعرَّف'
            ];
        }

        try {
            // Get photo details
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/photos/{$photoId}");

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'فشل في جلب تفاصيل الصورة'
                ];
            }

            $photoData = $response->json();
            $imageUrl = $photoData['src'][$size] ?? $photoData['src']['large'];

            // Download the image
            $imageResponse = Http::get($imageUrl);
            
            if (!$imageResponse->successful()) {
                return [
                    'success' => false,
                    'message' => 'فشل في تحميل الصورة'
                ];
            }

            $userId = auth()->id();
            $extension = 'jpg'; // Pexels images are usually JPG
            $storedFilename = Str::uuid() . '.' . $extension;
            $path = "admin-templates-assets/{$userId}/{$folder}/{$storedFilename}";

            // Save image to storage
            Storage::disk('public')->put($path, $imageResponse->body());

            // Get image metadata
            $tempPath = storage_path('app/public/' . $path);
            $metadata = [];
            
            try {
                $image = Image::make($tempPath);
                $metadata['dimensions'] = [
                    'width' => $image->width(),
                    'height' => $image->height()
                ];
            } catch (\Exception $e) {
                // Ignore if image processing fails
            }

            // Create database record
            $userFile = UserFile::create([
                'user_id' => $userId,
                'filename' => $photoData['alt'] ?? "Pexels Photo {$photoId}",
                'stored_filename' => $storedFilename,
                'path' => $path,
                'mime_type' => 'image/jpeg',
                'extension' => $extension,
                'size' => strlen($imageResponse->body()),
                'folder' => $folder,
                'metadata' => array_merge($metadata, [
                    'pexels_id' => $photoId,
                    'photographer' => $photoData['photographer'] ?? null,
                    'photographer_url' => $photoData['photographer_url'] ?? null,
                    'pexels_url' => $photoData['url'] ?? null
                ]),
                'source' => 'pexels',
                'source_id' => (string) $photoId,
                'description' => $photoData['alt'] ?? null,
                'tags' => []
            ]);

            return [
                'success' => true,
                'message' => 'تم تحميل الصورة بنجاح',
                'file' => $userFile
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في تحميل الصورة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format photos data for frontend.
     */
    private function formatPhotos(array $photos): array
    {
        return array_map(function ($photo) {
            return [
                'id' => $photo['id'],
                'width' => $photo['width'],
                'height' => $photo['height'],
                'url' => $photo['url'],
                'photographer' => $photo['photographer'],
                'photographer_url' => $photo['photographer_url'],
                'photographer_id' => $photo['photographer_id'],
                'avg_color' => $photo['avg_color'],
                'alt' => $photo['alt'],
                'src' => [
                    'original' => $photo['src']['original'],
                    'large2x' => $photo['src']['large2x'],
                    'large' => $photo['src']['large'],
                    'medium' => $photo['src']['medium'],
                    'small' => $photo['src']['small'],
                    'portrait' => $photo['src']['portrait'],
                    'landscape' => $photo['src']['landscape'],
                    'tiny' => $photo['src']['tiny']
                ]
            ];
        }, $photos);
    }

    /**
     * Check if API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
