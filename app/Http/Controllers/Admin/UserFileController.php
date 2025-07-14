<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserFileController extends Controller
{
    /**
     * Get user files with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UserFile::forUser(auth()->id())
            ->orderBy('created_at', 'desc');

        // Filter by folder
        if ($request->filled('folder')) {
            $query->inFolder($request->folder);
        }

        // Filter by type (images only for now)
        if ($request->filled('type') && $request->type === 'images') {
            $query->images();
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $files = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'files' => $files,
            'storage_info' => $this->getStorageInfo()
        ]);
    }

    /**
     * Upload a new file.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:png,jpg,jpeg,svg|max:10240', // 10MB max
            'folder' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المرسلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check storage limit (100MB per user)
        $currentUsage = $this->getCurrentStorageUsage();
        $fileSize = $request->file('file')->getSize();

        if (($currentUsage + $fileSize) > (100 * 1024 * 1024)) { // 100MB
            return response()->json([
                'success' => false,
                'message' => 'تم تجاوز الحد الأقصى للتخزين (100 ميجابايت)'
            ], 413);
        }

        $file = $request->file('file');
        $userId = auth()->id();
        $folder = $request->get('folder', 'general');

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $storedFilename = Str::uuid() . '.' . $extension;
        $path = "admin-templates-assets/{$userId}/{$folder}/{$storedFilename}";

        // Store file
        $file->storeAs("admin-templates-assets/{$userId}/{$folder}", $storedFilename, 'public');

        // Get image metadata if it's an image
        $metadata = [];
        if (str_starts_with($file->getMimeType(), 'image/')) {
            try {
                $image = Image::make($file);
                $metadata['dimensions'] = [
                    'width' => $image->width(),
                    'height' => $image->height()
                ];
            } catch (\Exception $e) {
                // Ignore if image processing fails
            }
        }

        // Create database record
        $userFile = UserFile::create([
            'user_id' => $userId,
            'filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'folder' => $folder,
            'metadata' => $metadata,
            'source' => 'upload',
            'description' => $request->get('description'),
            'tags' => $request->get('tags', [])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم رفع الملف بنجاح',
            'file' => $userFile,
            'storage_info' => $this->getStorageInfo()
        ]);
    }

    /**
     * Update file information.
     */
    public function update(Request $request, UserFile $userFile): JsonResponse
    {
        // Check ownership
        if ($userFile->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا الملف'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'filename' => 'nullable|string|max:255',
            'folder' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المرسلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $userFile->update($request->only(['filename', 'folder', 'description', 'tags']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملف بنجاح',
            'file' => $userFile->fresh()
        ]);
    }

    /**
     * Delete a file.
     */
    public function destroy(UserFile $userFile): JsonResponse
    {
        // Check ownership
        if ($userFile->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذا الملف'
            ], 403);
        }

        // Delete file from storage
        $userFile->deleteFile();

        // Delete database record
        $userFile->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الملف بنجاح',
            'storage_info' => $this->getStorageInfo()
        ]);
    }

    /**
     * Get folders list for current user.
     */
    public function folders(): JsonResponse
    {
        $folders = UserFile::forUser(auth()->id())
            ->select('folder')
            ->distinct()
            ->pluck('folder')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'folders' => $folders
        ]);
    }

    /**
     * Get storage information for current user.
     */
    public function storageInfo(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'storage_info' => $this->getStorageInfo()
        ]);
    }

    /**
     * Mark file as used (update last_used_at).
     */
    public function markAsUsed(UserFile $userFile): JsonResponse
    {
        // Check ownership
        if ($userFile->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول لهذا الملف'
            ], 403);
        }

        $userFile->markAsUsed();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث آخر استخدام'
        ]);
    }

    /**
     * Get current storage usage for user.
     */
    private function getCurrentStorageUsage(): int
    {
        return UserFile::forUser(auth()->id())->sum('size');
    }

    /**
     * Get storage information.
     */
    private function getStorageInfo(): array
    {
        $used = $this->getCurrentStorageUsage();
        $limit = 100 * 1024 * 1024; // 100MB
        $percentage = $limit > 0 ? round(($used / $limit) * 100, 2) : 0;

        return [
            'used' => $used,
            'used_human' => $this->formatBytes($used),
            'limit' => $limit,
            'limit_human' => $this->formatBytes($limit),
            'available' => $limit - $used,
            'available_human' => $this->formatBytes($limit - $used),
            'percentage' => $percentage
        ];
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
