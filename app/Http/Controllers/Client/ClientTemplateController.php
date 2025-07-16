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

        return Inertia::render('Client/DesignEditor', [
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'design_data' => $template->design_data,
                'editable_elements' => $template->editable_elements,
                'canvas_size' => $template->canvas_size,
                'background_image_url' => $template->background_image_url,
                'category' => $template->category->name ?? null,
            ],
            'mode' => 'create'
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

        return Inertia::render('Client/DesignEditor', [
            'clientTemplate' => [
                'id' => $clientTemplate->id,
                'name' => $clientTemplate->name,
                'design_data' => $clientTemplate->design_data_with_watermark,
                'editable_elements' => $clientTemplate->editable_elements,
                'canvas_size' => $clientTemplate->canvas_size,
                'version' => $clientTemplate->version,
                'notes' => $clientTemplate->notes,
            ],
            'template' => [
                'id' => $clientTemplate->template->id,
                'name' => $clientTemplate->template->name,
                'editable_elements' => $clientTemplate->template->editable_elements,
                'background_image_url' => $clientTemplate->template->background_image_url,
            ],
            'mode' => 'edit'
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

            // Créer le design client
            $clientTemplate = ClientTemplate::create([
                'user_id' => auth()->id(),
                'template_id' => $request->template_id,
                'name' => $request->name,
                'design_data' => $request->design_data,
                'editable_elements' => $request->editable_elements,
                'canvas_size' => $request->canvas_size ?? $template->canvas_size,
                'notes' => $request->notes,
                'last_edited_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التصميم بنجاح',
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
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التصميم'
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
     * Remove the specified client template.
     */
    public function destroy(ClientTemplate $clientTemplate)
    {
        // Vérifier que le design appartient à l'utilisateur connecté
        if ($clientTemplate->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذا التصميم'
            ], 403);
        }

        try {
            // Supprimer la miniature si elle existe
            if ($clientTemplate->thumbnail) {
                \Storage::disk('public')->delete($clientTemplate->thumbnail);
            }

            $clientTemplate->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التصميم بنجاح'
            ]);

        } catch (\Exception $e) {
            \Log::error('Client template delete error: ' . $e->getMessage(), [
                'client_template_id' => $clientTemplate->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف التصميم'
            ], 500);
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
            $duplicatedTemplate = $clientTemplate->replicate();
            $duplicatedTemplate->name = $clientTemplate->name . ' - نسخة';
            $duplicatedTemplate->version = 1;
            $duplicatedTemplate->thumbnail = null; // Will be generated later
            $duplicatedTemplate->last_edited_at = now();
            $duplicatedTemplate->save();

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
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء نسخ التصميم'
            ], 500);
        }
    }
}
