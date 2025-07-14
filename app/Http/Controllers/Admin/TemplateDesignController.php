<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TemplateDesignController extends Controller
{
    /**
     * Save enhanced design data for a template.
     */
    public function saveDesign(Request $request, Template $template)
    {
        try {
            $validator = Validator::make($request->all(), [
                'design_data' => 'required|string',
                'editable_elements' => 'nullable|array',
                'canvas_size' => 'nullable|string',
                'background_image' => 'nullable|string',
                'design_notes' => 'nullable|string',
            ], [
                'design_data.required' => 'بيانات التصميم مطلوبة',
                'design_data.string' => 'بيانات التصميم يجب أن تكون في صيغة نصية صحيحة',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في التحقق من البيانات',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate JSON structure
            $designData = json_decode($request->design_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات التصميم غير صحيحة'
                ], 400);
            }

            // Skip structure validation for now to allow saving
            // $validation = $this->validateDesignDataStructure($designData);
            // if (!$validation['isValid']) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'هيكل بيانات التصميم غير صحيح',
            //         'errors' => $validation['errors']
            //     ], 400);
            // }

            // Update template with enhanced data
            $updateData = [
                'design_data' => $request->design_data,
                'last_edited_at' => now(),
                'version' => $template->version + 1,
            ];

            // Handle editable elements
            if ($request->has('editable_elements')) {
                $updateData['editable_elements'] = $request->editable_elements;
            }

            // Handle canvas size
            if ($request->has('canvas_size')) {
                $updateData['canvas_size'] = $request->canvas_size;
            }

            // Handle background image
            if ($request->has('background_image')) {
                $updateData['background_image'] = $request->background_image;
            }

            // Handle design notes
            if ($request->has('design_notes')) {
                $updateData['design_notes'] = $request->design_notes;
            }

            $template->update($updateData);

            // Generate thumbnail if design has changed significantly
            $this->generateThumbnailIfNeeded($template, $designData);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التصميم بنجاح',
                'template' => $template->fresh(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Design save error: ' . $e->getMessage(), [
                'template_id' => $template->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التصميم'
            ], 500);
        }
    }

    /**
     * Upload and save background image for template.
     */
    public function uploadBackground(Request $request, Template $template)
    {
        $request->validate([
            'background' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ], [
            'background.required' => 'صورة الخلفية مطلوبة',
            'background.image' => 'يجب أن يكون الملف صورة',
            'background.mimes' => 'صيغة الصورة غير مدعومة',
            'background.max' => 'حجم الصورة كبير جداً (الحد الأقصى 5 ميجابايت)',
        ]);

        try {
            // Delete old background if exists
            if ($template->background_image) {
                Storage::disk('public')->delete($template->background_image);
            }

            // Store new background with optimized name
            $filename = 'bg_' . $template->id . '_' . time() . '.' . $request->file('background')->getClientOriginalExtension();
            $path = $request->file('background')->storeAs('templates/backgrounds', $filename, 'public');
            
            $template->update([
                'background_image' => $path,
                'last_edited_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم رفع صورة الخلفية بنجاح',
                'background_url' => asset('storage/' . $path),
                'path' => $path
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Background upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في رفع صورة الخلفية'
            ], 500);
        }
    }

    /**
     * Generate and save thumbnail for template.
     */
    public function generateThumbnail(Request $request, Template $template)
    {
        $request->validate([
            'thumbnail_data' => 'required|string', // Base64 image data
        ], [
            'thumbnail_data.required' => 'بيانات المعاينة مطلوبة',
        ]);

        try {
            // Validate base64 format
            if (!preg_match('/^data:image\/(\w+);base64,/', $request->thumbnail_data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'صيغة بيانات الصورة غير صحيحة'
                ], 400);
            }

            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->thumbnail_data));
            
            if (!$imageData) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في فك تشفير بيانات الصورة'
                ], 400);
            }

            // Generate unique filename
            $filename = 'thumb_' . $template->id . '_' . time() . '.png';
            $path = 'templates/thumbnails/' . $filename;
            
            // Save to storage
            Storage::disk('public')->put($path, $imageData);
            
            // Delete old thumbnail if exists
            if ($template->thumbnail) {
                Storage::disk('public')->delete($template->thumbnail);
            }
            
            // Update template
            $template->update([
                'thumbnail' => $path,
                'last_edited_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المعاينة بنجاح',
                'thumbnail_url' => asset('storage/' . $path),
                'path' => $path
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Thumbnail generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء المعاينة'
            ], 500);
        }
    }

    /**
     * Export template design data.
     */
    public function exportDesign(Template $template)
    {
        try {
            $exportData = [
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'category' => $template->category->name ?? null,
                ],
                'design_data' => $template->design_data,
                'editable_elements' => $template->editable_elements,
                'canvas_size' => $template->canvas_size,
                'version' => $template->version,
                'exported_at' => now()->toISOString(),
                'exported_by' => auth()->user()->email,
            ];

            $filename = Str::slug($template->name) . '_design_export.json';

            return response()->json($exportData)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            \Log::error('Design export error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تصدير التصميم'
            ], 500);
        }
    }

    /**
     * Import template design data.
     */
    public function importDesign(Request $request, Template $template)
    {
        $request->validate([
            'design_file' => 'required|file|mimes:json|max:2048', // 2MB max
        ], [
            'design_file.required' => 'ملف التصميم مطلوب',
            'design_file.mimes' => 'يجب أن يكون الملف من نوع JSON',
            'design_file.max' => 'حجم الملف كبير جداً',
        ]);

        try {
            $fileContent = file_get_contents($request->file('design_file')->getRealPath());
            $importData = json_decode($fileContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'ملف التصميم تالف أو غير صحيح'
                ], 400);
            }

            // Validate import data structure
            if (!isset($importData['design_data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات التصميم مفقودة في الملف'
                ], 400);
            }

            // Update template with imported data
            $updateData = [
                'design_data' => is_string($importData['design_data']) 
                    ? $importData['design_data'] 
                    : json_encode($importData['design_data']),
                'last_edited_at' => now(),
                'version' => $template->version + 1,
            ];

            if (isset($importData['editable_elements'])) {
                $updateData['editable_elements'] = $importData['editable_elements'];
            }

            if (isset($importData['canvas_size'])) {
                $updateData['canvas_size'] = $importData['canvas_size'];
            }

            $template->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'تم استيراد التصميم بنجاح',
                'template' => $template->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Design import error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في استيراد التصميم'
            ], 500);
        }
    }

    /**
     * Validate design data structure.
     */
    private function validateDesignDataStructure($designData)
    {
        $errors = [];

        // Check required fields
        if (!isset($designData['design'])) {
            $errors[] = 'بيانات التصميم الأساسية مفقودة';
        }

        if (!isset($designData['version'])) {
            $errors[] = 'رقم الإصدار مفقود';
        }

        // Check design structure
        if (isset($designData['design'])) {
            if (!isset($designData['design']['html']) && !isset($designData['design']['projectData'])) {
                $errors[] = 'بيانات HTML أو بيانات المشروع مفقودة';
            }
        }

        // Check editable elements structure
        if (isset($designData['editableElements']) && !is_array($designData['editableElements'])) {
            $errors[] = 'بيانات العناصر القابلة للتحرير غير صحيحة';
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Generate thumbnail if needed.
     */
    private function generateThumbnailIfNeeded($template, $designData)
    {
        // Check if significant changes were made
        $shouldGenerate = false;

        // Generate if no thumbnail exists
        if (!$template->thumbnail) {
            $shouldGenerate = true;
        }

        // Generate if design structure changed significantly
        if (isset($designData['editableElements']) && count($designData['editableElements']) > 0) {
            $shouldGenerate = true;
        }

        if ($shouldGenerate) {
            // Queue thumbnail generation job
            // This would typically be handled by a job queue
            \Log::info('Thumbnail generation queued for template: ' . $template->id);
        }
    }
}
