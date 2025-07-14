<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PexelsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PexelsController extends Controller
{
    private PexelsService $pexelsService;

    public function __construct(PexelsService $pexelsService)
    {
        $this->pexelsService = $pexelsService;
    }

    /**
     * Search for photos on Pexels.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:100',
            'page' => 'nullable|integer|min:1|max:100',
            'per_page' => 'nullable|integer|min:1|max:80'
        ]);

        $result = $this->pexelsService->searchPhotos(
            $request->get('query'),
            $request->get('page', 1),
            $request->get('per_page', 20)
        );

        return response()->json($result);
    }

    /**
     * Get curated photos from Pexels.
     */
    public function curated(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'nullable|integer|min:1|max:100',
            'per_page' => 'nullable|integer|min:1|max:80'
        ]);

        $result = $this->pexelsService->getCuratedPhotos(
            $request->get('page', 1),
            $request->get('per_page', 20)
        );

        return response()->json($result);
    }

    /**
     * Download a photo from Pexels.
     */
    public function download(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'photo_id' => 'required|integer',
                'size' => 'nullable|string|in:original,large2x,large,medium,small',
                'folder' => 'nullable|string|max:50'
            ]);

            // Check if Pexels is configured
            if (!$this->pexelsService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خدمة Pexels غير مُعرَّفة. يرجى إضافة مفتاح API في ملف .env'
                ], 503);
            }

            $result = $this->pexelsService->downloadPhoto(
                $request->get('photo_id'),
                $request->get('size', 'large'),
                $request->get('folder', 'pexels')
            );

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Pexels download error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحميل الصورة من Pexels'
            ], 500);
        }
    }

    /**
     * Check if Pexels API is configured.
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'configured' => $this->pexelsService->isConfigured(),
            'message' => $this->pexelsService->isConfigured()
                ? 'Pexels API مُعرَّف ومتاح'
                : 'Pexels API غير مُعرَّف'
        ]);
    }
}
