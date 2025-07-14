<?php

namespace App\Services;

use App\Models\UserFile;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackgroundRemovalService
{
    private string $removeBgApiKey;
    private string $removeBgUrl = 'https://api.remove.bg/v1.0/removebg';

    public function __construct()
    {
        $this->removeBgApiKey = config('services.removebg.api_key', env('REMOVEBG_API_KEY'));
    }

    /**
     * Remove background from image using Remove.bg API.
     */
    public function removeBackground(UserFile $userFile): array
    {
        if (empty($this->removeBgApiKey)) {
            return [
                'success' => false,
                'message' => 'مفتاح API الخاص بـ Remove.bg غير مُعرَّف'
            ];
        }

        if (!$userFile->is_image) {
            return [
                'success' => false,
                'message' => 'الملف المحدد ليس صورة'
            ];
        }

        try {
            // Get the image file path
            $imagePath = storage_path('app/public/' . $userFile->path);
            
            if (!file_exists($imagePath)) {
                return [
                    'success' => false,
                    'message' => 'الملف غير موجود'
                ];
            }

            // Prepare the request
            $response = Http::withHeaders([
                'X-Api-Key' => $this->removeBgApiKey
            ])->attach(
                'image_file', 
                file_get_contents($imagePath), 
                $userFile->filename
            )->post($this->removeBgUrl, [
                'size' => 'auto',
                'format' => 'png'
            ]);

            if ($response->successful()) {
                // Save the processed image
                $processedImageData = $response->body();
                
                // Generate new filename
                $originalName = pathinfo($userFile->filename, PATHINFO_FILENAME);
                $newFilename = $originalName . '_no_bg_' . time() . '.png';
                $storedFilename = Str::uuid() . '.png';
                $newPath = "admin-templates-assets/{$userFile->user_id}/processed/{$storedFilename}";

                // Store the processed image
                Storage::disk('public')->put($newPath, $processedImageData);

                // Create new UserFile record
                $processedFile = UserFile::create([
                    'user_id' => $userFile->user_id,
                    'filename' => $newFilename,
                    'stored_filename' => $storedFilename,
                    'path' => $newPath,
                    'mime_type' => 'image/png',
                    'extension' => 'png',
                    'size' => strlen($processedImageData),
                    'folder' => 'processed',
                    'metadata' => [
                        'original_file_id' => $userFile->id,
                        'processing_type' => 'background_removal',
                        'api_used' => 'remove.bg'
                    ],
                    'source' => 'processed',
                    'source_id' => (string) $userFile->id,
                    'description' => 'صورة بدون خلفية - ' . $userFile->filename,
                    'tags' => ['processed', 'no-background']
                ]);

                return [
                    'success' => true,
                    'message' => 'تم إزالة الخلفية بنجاح',
                    'original_file' => $userFile,
                    'processed_file' => $processedFile
                ];

            } else {
                // Handle API errors
                $errorData = $response->json();
                $errorMessage = $this->getErrorMessage($response->status(), $errorData);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في معالجة الصورة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Remove background from image URL.
     */
    public function removeBackgroundFromUrl(string $imageUrl, int $userId): array
    {
        if (empty($this->removeBgApiKey)) {
            return [
                'success' => false,
                'message' => 'مفتاح API الخاص بـ Remove.bg غير مُعرَّف'
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->removeBgApiKey
            ])->post($this->removeBgUrl, [
                'image_url' => $imageUrl,
                'size' => 'auto',
                'format' => 'png'
            ]);

            if ($response->successful()) {
                // Save the processed image
                $processedImageData = $response->body();
                
                // Generate filename
                $filename = 'processed_image_' . time() . '.png';
                $storedFilename = Str::uuid() . '.png';
                $path = "admin-templates-assets/{$userId}/processed/{$storedFilename}";

                // Store the processed image
                Storage::disk('public')->put($path, $processedImageData);

                // Create UserFile record
                $processedFile = UserFile::create([
                    'user_id' => $userId,
                    'filename' => $filename,
                    'stored_filename' => $storedFilename,
                    'path' => $path,
                    'mime_type' => 'image/png',
                    'extension' => 'png',
                    'size' => strlen($processedImageData),
                    'folder' => 'processed',
                    'metadata' => [
                        'source_url' => $imageUrl,
                        'processing_type' => 'background_removal',
                        'api_used' => 'remove.bg'
                    ],
                    'source' => 'processed',
                    'description' => 'صورة بدون خلفية من رابط',
                    'tags' => ['processed', 'no-background', 'from-url']
                ]);

                return [
                    'success' => true,
                    'message' => 'تم إزالة الخلفية بنجاح',
                    'processed_file' => $processedFile
                ];

            } else {
                $errorData = $response->json();
                $errorMessage = $this->getErrorMessage($response->status(), $errorData);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في معالجة الصورة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get account information from Remove.bg API.
     */
    public function getAccountInfo(): array
    {
        if (empty($this->removeBgApiKey)) {
            return [
                'success' => false,
                'message' => 'مفتاح API الخاص بـ Remove.bg غير مُعرَّف'
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->removeBgApiKey
            ])->get('https://api.remove.bg/v1.0/account');

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'account_info' => [
                        'credits_remaining' => $data['attributes']['credits']['remaining'] ?? 0,
                        'credits_total' => $data['attributes']['credits']['total'] ?? 0,
                        'api_calls_remaining' => $data['attributes']['api']['free_calls'] ?? 0
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'فشل في جلب معلومات الحساب'
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في الاتصال بـ Remove.bg: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if Remove.bg API is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->removeBgApiKey);
    }

    /**
     * Get user-friendly error message based on API response.
     */
    private function getErrorMessage(int $statusCode, ?array $errorData): string
    {
        switch ($statusCode) {
            case 400:
                return 'خطأ في البيانات المرسلة - تأكد من صحة الصورة';
            case 402:
                return 'تم استنفاد الرصيد المتاح - يرجى ترقية الحساب';
            case 403:
                return 'مفتاح API غير صحيح أو منتهي الصلاحية';
            case 429:
                return 'تم تجاوز الحد المسموح من الطلبات - حاول مرة أخرى لاحقاً';
            case 500:
                return 'خطأ في خادم Remove.bg - حاول مرة أخرى لاحقاً';
            default:
                $message = $errorData['errors'][0]['title'] ?? 'خطأ غير معروف';
                return "خطأ في إزالة الخلفية: {$message}";
        }
    }

    /**
     * Estimate credits needed for image processing.
     */
    public function estimateCredits(UserFile $userFile): int
    {
        // Remove.bg pricing is usually 1 credit per image
        // But can vary based on image size and complexity
        $fileSize = $userFile->size;
        
        if ($fileSize > 10 * 1024 * 1024) { // > 10MB
            return 2; // Larger images might cost more
        }
        
        return 1; // Standard cost
    }

    /**
     * Get supported image formats.
     */
    public function getSupportedFormats(): array
    {
        return [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/webp'
        ];
    }

    /**
     * Check if image format is supported.
     */
    public function isFormatSupported(string $mimeType): bool
    {
        return in_array($mimeType, $this->getSupportedFormats());
    }
}
