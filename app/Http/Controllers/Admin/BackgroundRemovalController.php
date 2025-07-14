<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserFile;
use App\Services\BackgroundRemovalService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BackgroundRemovalController extends Controller
{
    private BackgroundRemovalService $backgroundRemovalService;

    public function __construct(BackgroundRemovalService $backgroundRemovalService)
    {
        $this->backgroundRemovalService = $backgroundRemovalService;
    }

    /**
     * Remove background from an existing user file.
     */
    public function removeFromFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file_id' => 'required|integer|exists:user_files,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المرسلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the user file
        $userFile = UserFile::where('id', $request->file_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$userFile) {
            return response()->json([
                'success' => false,
                'message' => 'الملف غير موجود أو غير مصرح لك بالوصول إليه'
            ], 404);
        }

        // Check if format is supported
        if (!$this->backgroundRemovalService->isFormatSupported($userFile->mime_type)) {
            return response()->json([
                'success' => false,
                'message' => 'تنسيق الصورة غير مدعوم. الصيغ المدعومة: JPG, PNG, WebP'
            ], 422);
        }

        // Process the image
        $result = $this->backgroundRemovalService->removeBackground($userFile);

        return response()->json($result);
    }

    /**
     * Remove background from image URL.
     */
    public function removeFromUrl(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المرسلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->backgroundRemovalService->removeBackgroundFromUrl(
            $request->image_url,
            auth()->id()
        );

        return response()->json($result);
    }

    /**
     * Get account information and remaining credits.
     */
    public function accountInfo(): JsonResponse
    {
        $result = $this->backgroundRemovalService->getAccountInfo();
        return response()->json($result);
    }

    /**
     * Check if background removal service is configured.
     */
    public function status(): JsonResponse
    {
        $isConfigured = $this->backgroundRemovalService->isConfigured();

        $response = [
            'success' => true,
            'configured' => $isConfigured,
            'message' => $isConfigured
                ? 'خدمة إزالة الخلفية متاحة'
                : 'خدمة إزالة الخلفية غير مُعرَّفة',
            'supported_formats' => $this->backgroundRemovalService->getSupportedFormats()
        ];

        if ($isConfigured) {
            $accountInfo = $this->backgroundRemovalService->getAccountInfo();
            if ($accountInfo['success']) {
                $response['account_info'] = $accountInfo['account_info'];
            }
        }

        return response()->json($response);
    }

    /**
     * Estimate credits needed for processing a file.
     */
    public function estimateCredits(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file_id' => 'required|integer|exists:user_files,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المرسلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $userFile = UserFile::where('id', $request->file_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$userFile) {
            return response()->json([
                'success' => false,
                'message' => 'الملف غير موجود'
            ], 404);
        }

        $estimatedCredits = $this->backgroundRemovalService->estimateCredits($userFile);

        return response()->json([
            'success' => true,
            'estimated_credits' => $estimatedCredits,
            'file_size_mb' => round($userFile->size / (1024 * 1024), 2)
        ]);
    }
}
