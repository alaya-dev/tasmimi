<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientTemplate;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ClientTemplateController extends Controller
{
    /**
     * Display a listing of the client's templates.
     */
    public function index(): Response
    {
        $clientTemplates = auth()->user()->clientTemplates()
            ->with('template.category')
            ->orderBy('last_edited_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Client/MyDesigns', [
            'designs' => $clientTemplates->map(function ($clientTemplate) {
                return [
                    'id' => $clientTemplate->id,
                    'name' => $clientTemplate->name,
                    'thumbnail_url' => $clientTemplate->thumbnail_url,
                    'generated_image_url' => asset('images/samqa-watermark.svg'), // Image placeholder par défaut
                    'canvas_size' => $clientTemplate->canvas_size,
                    'version' => $clientTemplate->version,
                    'last_edited_at' => $clientTemplate->last_edited_at?->format('Y-m-d H:i:s'),
                    'created_at' => $clientTemplate->created_at->format('Y-m-d H:i:s'),
                    'template' => [
                        'id' => $clientTemplate->template->id,
                        'name' => $clientTemplate->template->name,
                        'category' => $clientTemplate->template->category->name ?? null,
                    ],
                ];
            })
        ]);
    }

    /**
     * Show the editor for creating a new design based on a template.
     */
    public function create(Template $template): Response
    {
        // Vérifier que le template est actif
        if (!$template->is_active) {
            abort(404, 'Template non disponible');
        }

        $user = auth()->user();
        $hasActiveSubscription = $user->hasActiveSubscription();
        $hasPurchasedTemplate = $user->hasPurchasedTemplate($template->id);

        // Allow access to editor for ALL users regardless of template price
        // Restrictions will be applied in the editor interface for save/download functionality

        return Inertia::render('Client/UnifiedDesignEditor', [
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'design_data' => $template->design_data,
                'editable_elements' => $template->editable_elements,
                'canvas_size' => $template->canvas_size,
                'background_image_url' => $template->background_image_url,
                'category' => $template->category->name ?? null,
                'price' => $template->price,
            ],
            'mode' => 'create',
            'hasActiveSubscription' => $hasActiveSubscription,
            'hasPurchasedTemplate' => $hasPurchasedTemplate,
            'templatePrice' => $template->price,
            'canSaveAndDownload' => $template->isFree() || $hasActiveSubscription || $hasPurchasedTemplate,
        ]);
    }

    /**
     * Show the editor for editing an existing client design.
     */
    public function edit(ClientTemplate $clientTemplate): Response
    {
        // Vérifier que le design appartient à l'utilisateur connecté
        if ($clientTemplate->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        $user = auth()->user();
        $hasActiveSubscription = $user->hasActiveSubscription();

        return Inertia::render('Client/UnifiedDesignEditor', [
            'template' => [
                'id' => $clientTemplate->id,
                'name' => $clientTemplate->name,
                'design_data' => $clientTemplate->design_data_with_watermark,
                'editable_elements' => $clientTemplate->editable_elements,
                'canvas_size' => $clientTemplate->canvas_size,
                'version' => $clientTemplate->version,
                'notes' => $clientTemplate->notes,
                'price' => $clientTemplate->template->price,
            ],
            'originalTemplate' => [
                'id' => $clientTemplate->template->id,
                'name' => $clientTemplate->template->name,
                'editable_elements' => $clientTemplate->template->editable_elements,
                'background_image_url' => $clientTemplate->template->background_image_url,
                'price' => $clientTemplate->template->price,
            ],
            'mode' => 'edit',
            'hasActiveSubscription' => $hasActiveSubscription,
            'templatePrice' => $clientTemplate->template->price,
        ]);
    }

    /**
     * Store a newly created client template.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'template_id' => 'required|exists:templates,id',
                'name' => 'required|string|max:255',
                'design_data' => 'required|string',
                'editable_elements' => 'nullable|array',
                'canvas_size' => 'nullable|string',
                'notes' => 'nullable|string|max:1000',
            ], [
                'template_id.required' => 'معرف القالب مطلوب',
                'template_id.exists' => 'القالب المحدد غير موجود',
                'name.required' => 'اسم التصميم مطلوب',
                'name.max' => 'اسم التصميم يجب ألا يتجاوز 255 حرف',
                'design_data.required' => 'بيانات التصميم مطلوبة',
                'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في التحقق من البيانات',
                    'errors' => $validator->errors()
                ], 422);
            }

            \Log::info('Client template store attempt', [
                'user_id' => auth()->id(),
                'template_id' => $request->template_id,
                'name' => $request->name,
                'data_size' => strlen($request->design_data ?? ''),
            ]);

            // Vérifier que le template existe et est actif
            $template = Template::where('id', $request->template_id)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'القالب المحدد غير متاح'
                ], 404);
            }

        // Check if user can save this template (for paid templates)
        $user = auth()->user();
        $hasActiveSubscription = $user->hasActiveSubscription();
        $hasPurchasedTemplate = $user->hasPurchasedTemplate($template->id);

        if ($template->isPaid() && !$hasActiveSubscription && !$hasPurchasedTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'يجب شراء هذا القالب أو الاشتراك لحفظ التصميم',
                'requires_payment' => true,
                'template_price' => $template->price,
                'purchase_url' => route('client.templates.purchase', $template)
            ], 403);
        }

            // Check if we're updating a specific client template (from edit mode)
            // or if we need to find/create one for this user and template
            $existingClientTemplate = null;

            // If we have a client_template_id in the request, we're updating a specific template
            if ($request->has('client_template_id')) {
                $existingClientTemplate = ClientTemplate::where('id', $request->client_template_id)
                    ->where('user_id', auth()->id())
                    ->first();
            } else {
                // Otherwise, check if client template already exists for this user and template
                $existingClientTemplate = ClientTemplate::where('user_id', auth()->id())
                    ->where('template_id', $request->template_id)
                    ->first();
            }

            if ($existingClientTemplate) {
                // Update existing client template
                // Check if we're trying to update with a name that would cause a unique constraint violation
                $nameToUse = $request->name;

                // If the name is different and would cause a conflict, generate a unique name
                if ($existingClientTemplate->name !== $request->name) {
                    $conflictCheck = ClientTemplate::where('user_id', auth()->id())
                        ->where('template_id', $request->template_id)
                        ->where('name', $request->name)
                        ->where('id', '!=', $existingClientTemplate->id)
                        ->exists();

                    if ($conflictCheck) {
                        // Generate a unique name by appending timestamp
                        $nameToUse = $request->name . ' - ' . now()->format('H:i:s');
                    }
                }

                $existingClientTemplate->update([
                    'name' => $nameToUse,
                    'design_data' => $request->design_data,
                    'editable_elements' => $request->editable_elements,
                    'canvas_size' => $request->canvas_size ?? $template->canvas_size,
                    'notes' => $request->notes,
                    'last_edited_at' => now(),
                    'version' => ($existingClientTemplate->version ?? 0) + 1,
                ]);

                $clientTemplate = $existingClientTemplate;
                $action = 'updated';
            } else {
                // Create new client template
                $clientTemplate = ClientTemplate::create([
                    'user_id' => auth()->id(),
                    'template_id' => $request->template_id,
                    'name' => $request->name,
                    'design_data' => $request->design_data,
                    'editable_elements' => $request->editable_elements,
                    'canvas_size' => $request->canvas_size ?? $template->canvas_size,
                    'notes' => $request->notes,
                    'last_edited_at' => now(),
                    'version' => 1,
                ]);

                $action = 'created';
            }

            $message = $action === 'created' ? 'تم إنشاء التصميم بنجاح' : 'تم تحديث التصميم بنجاح';

            return response()->json([
                'success' => true,
                'message' => $message,
                'action' => $action,
                'client_template' => [
                    'id' => $clientTemplate->id,
                    'name' => $clientTemplate->name,
                    'version' => $clientTemplate->version,
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Client template save error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'template_id' => $request->template_id ?? null,
                'request_data' => $request->all(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التصميم',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified client template.
     */
    public function update(Request $request, ClientTemplate $clientTemplate)
    {
        // Vérifier que le design appartient à l'utilisateur connecté
        if ($clientTemplate->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا التصميم'
            ], 403);
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'design_data' => 'sometimes|required|string',
                'editable_elements' => 'nullable|array',
                'notes' => 'nullable|string|max:1000',
            ], [
                'name.required' => 'اسم التصميم مطلوب',
                'name.max' => 'اسم التصميم يجب ألا يتجاوز 255 حرف',
                'design_data.required' => 'بيانات التصميم مطلوبة',
                'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في التحقق من البيانات',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only(['name', 'design_data', 'editable_elements', 'notes']);
            $updateData['last_edited_at'] = now();
            $updateData['version'] = $clientTemplate->version + 1;

            $clientTemplate->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التصميم بنجاح',
                'client_template' => [
                    'id' => $clientTemplate->id,
                    'name' => $clientTemplate->name,
                    'version' => $clientTemplate->version,
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Client template update error: ' . $e->getMessage(), [
                'client_template_id' => $clientTemplate->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث التصميم'
            ], 500);
        }
    }

    /**
     * Update only the name of the specified client template.
     */
    public function updateName(Request $request, ClientTemplate $clientTemplate)
    {
        // Vérifier que le design appartient à l'utilisateur connecté
        if ($clientTemplate->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بتعديل هذا التصميم');
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ], [
                'name.required' => 'اسم التصميم مطلوب',
                'name.max' => 'اسم التصميم يجب ألا يتجاوز 255 حرف',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في التحقق من البيانات',
                    'errors' => $validator->errors()
                ], 422);
            }

            $clientTemplate->update([
                'name' => $request->name,
                'last_edited_at' => now(),
            ]);

            return redirect()->back()->with('success', 'تم تحديث اسم التصميم بنجاح');

        } catch (\Exception $e) {
            \Log::error('Client template name update error: ' . $e->getMessage(), [
                'client_template_id' => $clientTemplate->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث اسم التصميم');
        }
    }

    /**
     * Remove the specified client template.
     */
    public function destroy(ClientTemplate $clientTemplate)
    {
        // Vérifier que le design appartient à l'utilisateur connecté
        if ($clientTemplate->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بحذف هذا التصميم');
        }

        try {
            // Supprimer la miniature si elle existe
            if ($clientTemplate->thumbnail) {
                \Storage::disk('public')->delete($clientTemplate->thumbnail);
            }

            // Supprimer l'image générée si elle existe
            if ($clientTemplate->generated_image_url) {
                \Storage::disk('public')->delete($clientTemplate->generated_image_url);
            }

            $clientTemplate->delete();

            return redirect()->back()->with('success', 'تم حذف التصميم بنجاح');

        } catch (\Exception $e) {
            \Log::error('Client template delete error: ' . $e->getMessage(), [
                'client_template_id' => $clientTemplate->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف التصميم');
        }
    }

    /**
     * Export client template in specified format.
     */
    public function export(Request $request, ClientTemplate $clientTemplate)
    {
        // Vérifier que le design appartient à l'utilisateur connecté
        if ($clientTemplate->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتصدير هذا التصميم'
            ], 403);
        }

        try {
            $validator = Validator::make($request->all(), [
                'format' => 'required|in:pdf,svg,png,jpeg',
                'quality' => 'nullable|integer|min:1|max:100',
                'width' => 'nullable|integer|min:100|max:4000',
                'height' => 'nullable|integer|min:100|max:4000',
            ], [
                'format.required' => 'صيغة التصدير مطلوبة',
                'format.in' => 'صيغة التصدير غير مدعومة',
                'quality.integer' => 'جودة التصدير يجب أن تكون رقم',
                'quality.min' => 'جودة التصدير يجب أن تكون على الأقل 1',
                'quality.max' => 'جودة التصدير يجب ألا تتجاوز 100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في معاملات التصدير',
                    'errors' => $validator->errors()
                ], 422);
            }

            $format = $request->format;
            $quality = $request->quality ?? 90;
            $width = $request->width ?? $clientTemplate->canvas_dimensions['width'];
            $height = $request->height ?? $clientTemplate->canvas_dimensions['height'];

            // Obtenir les données du design avec filigrane
            $designData = $clientTemplate->design_data_with_watermark;

            // Générer le nom du fichier
            $filename = $this->generateExportFilename($clientTemplate, $format);

            return response()->json([
                'success' => true,
                'export_data' => [
                    'design_data' => $designData,
                    'format' => $format,
                    'quality' => $quality,
                    'width' => $width,
                    'height' => $height,
                    'filename' => $filename,
                    'watermark' => [
                        'text' => 'سامقة للتصميم',
                        'enabled' => true,
                        'position' => 'bottom-right',
                        'style' => [
                            'fontSize' => '16px',
                            'color' => 'rgba(0, 0, 0, 0.4)',
                            'fontFamily' => 'Cairo, sans-serif',
                            'fontWeight' => 'bold',
                            'rotation' => '-15deg',
                            'opacity' => '0.6'
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Client template export error: ' . $e->getMessage(), [
                'client_template_id' => $clientTemplate->id,
                'user_id' => auth()->id(),
                'format' => $request->format ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير التصميم'
            ], 500);
        }
    }

    /**
     * Generate export filename.
     */
    private function generateExportFilename(ClientTemplate $clientTemplate, string $format): string
    {
        $safeName = preg_replace('/[^a-zA-Z0-9\-_\u0600-\u06FF]/', '_', $clientTemplate->name);
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "{$safeName}_{$timestamp}.{$format}";
    }

    /**
     * Duplicate a client template.
     */
    public function duplicate(ClientTemplate $clientTemplate)
    {
        // Vérifier que le design appartient à l'utilisateur connecté
        if ($clientTemplate->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بنسخ هذا التصميم'
            ], 403);
        }

        try {
            \Log::info('Starting client template duplication', [
                'original_id' => $clientTemplate->id,
                'user_id' => auth()->id(),
                'original_name' => $clientTemplate->name
            ]);

            // Generate a unique name for the duplicated template
            $baseName = $clientTemplate->name . ' - نسخة';
            $uniqueName = $this->generateUniqueName($baseName, auth()->id());

            $duplicatedTemplate = $clientTemplate->replicate();
            $duplicatedTemplate->name = $uniqueName;
            $duplicatedTemplate->version = 1;
            $duplicatedTemplate->thumbnail = null; // Will be generated later
            $duplicatedTemplate->last_edited_at = now();
            $duplicatedTemplate->user_id = auth()->id(); // Ensure correct user_id
            $duplicatedTemplate->save();

            \Log::info('Client template duplicated successfully', [
                'original_id' => $clientTemplate->id,
                'new_id' => $duplicatedTemplate->id,
                'new_name' => $uniqueName,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم نسخ التصميم بنجاح',
                'client_template' => [
                    'id' => $duplicatedTemplate->id,
                    'name' => $duplicatedTemplate->name,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Client template duplicate error: ' . $e->getMessage(), [
                'client_template_id' => $clientTemplate->id,
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء نسخ التصميم'
            ], 500);
        }
    }

    /**
     * Generate a unique name for the duplicated template
     */
    private function generateUniqueName($baseName, $userId)
    {
        $name = $baseName;
        $counter = 1;

        while (ClientTemplate::where('user_id', $userId)->where('name', $name)->exists()) {
            $name = $baseName . ' (' . $counter . ')';
            $counter++;
        }

        return $name;
    }

    /**
     * Save design data for a template (client version)
     */
    public function saveDesign(Request $request, Template $template)
    {
        try {
            $validator = Validator::make($request->all(), [
                'design_data' => 'required|string',
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

            // Check data size and optimize if necessary
            $dataSize = strlen($request->design_data);
            if ($dataSize > 16777215) { // 16MB limit for MEDIUMTEXT
                \Log::warning('Client design data too large', [
                    'template_id' => $template->id,
                    'user_id' => auth()->id(),
                    'data_size' => $dataSize
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'بيانات التصميم كبيرة جداً. يرجى تقليل حجم الصور أو عدد العناصر.'
                ], 413);
            }

            // Extract canvas size from design data
            $canvasSize = '800x600';
            if (isset($designData['canvas'])) {
                $width = $designData['canvas']['width'] ?? 800;
                $height = $designData['canvas']['height'] ?? 600;
                $canvasSize = "{$width}x{$height}";
            }

            // Find or create client template for this user and template
            $clientTemplate = ClientTemplate::firstOrCreate([
                'user_id' => auth()->id(),
                'template_id' => $template->id,
            ], [
                'name' => $template->name . ' - نسختي',
                'design_data' => $request->design_data,
                'canvas_size' => $canvasSize,
                'version' => 1,
                'last_edited_at' => now(),
            ]);

            // Update existing client template
            if (!$clientTemplate->wasRecentlyCreated) {
                $clientTemplate->design_data = $request->design_data;
                $clientTemplate->canvas_size = $canvasSize;
                $clientTemplate->last_edited_at = now();
                $clientTemplate->version = ($clientTemplate->version ?? 0) + 1;
                $clientTemplate->save();
            }



            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التصميم بنجاح',
                'client_template' => $clientTemplate->fresh(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Client design save error: ' . $e->getMessage(), [
                'template_id' => $template->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التصميم: ' . $e->getMessage()
            ], 500);
        }
    }






}
