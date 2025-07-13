<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index(Request $request): Response
    {
        $query = Template::with(['category', 'creator'])
            ->orderBy('created_at', 'desc');

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by name if provided
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $templates = $query->paginate(12);

        // Add thumbnail_url to each template
        $templates->getCollection()->transform(function ($template) {
            $template->thumbnail_url = $template->thumbnail ? asset('storage/' . $template->thumbnail) : null;
            return $template;
        });

        return Inertia::render('Admin/Templates/Index', [
            'templates' => $templates,
            'categories' => Category::all(),
            'filters' => $request->only(['category', 'status', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new template.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Templates/Create', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'اسم القالب مطلوب',
            'name.max' => 'اسم القالب طويل جداً',
            'category_id.required' => 'الفئة مطلوبة',
            'category_id.exists' => 'الفئة المحددة غير موجودة',
            'thumbnail.required' => 'الصورة المصغرة مطلوبة',
            'thumbnail.image' => 'يجب أن تكون الصورة من نوع صحيح',
            'thumbnail.mimes' => 'يجب أن تكون الصورة من نوع: jpeg, png, jpg, gif',
            'thumbnail.max' => 'حجم الصورة كبير جداً (الحد الأقصى 2MB)',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('templates/thumbnails', 'public');
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true; // Par défaut, les nouveaux templates sont actifs

        Template::create($validated);

        return back()->with('success', 'تم إنشاء القالب بنجاح');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(Template $template): Response
    {
        return Inertia::render('Admin/Templates/Edit', [
            'template' => $template->load('category'),
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, Template $template)
    {
        // Debug: Log the request data
        \Log::info('Template update request data:', [
            'all' => $request->all(),
            'name' => $request->get('name'),
            'category_id' => $request->get('category_id'),
            'has_thumbnail' => $request->hasFile('thumbnail')
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'اسم القالب مطلوب',
            'name.max' => 'اسم القالب طويل جداً',
            'category_id.required' => 'الفئة مطلوبة',
            'category_id.exists' => 'الفئة المحددة غير موجودة',
            'thumbnail.image' => 'يجب أن تكون الصورة من نوع صحيح',
            'thumbnail.mimes' => 'يجب أن تكون الصورة من نوع: jpeg, png, jpg, gif',
            'thumbnail.max' => 'حجم الصورة كبير جداً (الحد الأقصى 2MB)',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($template->thumbnail) {
                Storage::disk('public')->delete($template->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('templates/thumbnails', 'public');
        }



        // Update the template with validated data
        $template->update($validated);

        return back()->with('success', 'تم تحديث القالب بنجاح');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(Template $template)
    {
        // Delete thumbnail if exists
        if ($template->thumbnail) {
            Storage::disk('public')->delete($template->thumbnail);
        }

        $template->delete();

        return back()->with('success', 'تم حذف القالب بنجاح');
    }

    /**
     * Toggle the active status of the template.
     */
    public function toggleStatus(Template $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        $status = $template->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';
        
        return back()->with('success', $status . ' القالب بنجاح');
    }

    /**
     * Show the design editor for the specified template.
     */
    public function designEditor(Template $template): Response
    {
        return Inertia::render('Admin/Templates/NewDesignEditor', [
            'template' => $template->load('category'),
            'categories' => Category::all(),
            'locale' => app()->getLocale(),
            'translations' => [
                'save' => __('حفظ'),
                'loading' => __('جاري التحميل...'),
                'success' => __('تم بنجاح'),
                'error' => __('حدث خطأ'),
            ]
        ]);
    }

    /**
     * Save the design data for a template.
     */
    public function saveDesign(Request $request, Template $template)
    {
        $validated = $request->validate([
            'design_data' => 'required|string',
            'background_image' => 'nullable|string',
            'editable_elements' => 'nullable|array',
            'canvas_size' => 'nullable|string',
            'design_notes' => 'nullable|string',
        ]);

        // Parse design data to extract additional information
        $designData = json_decode($validated['design_data'], true);

        $template->update([
            'design_data' => $validated['design_data'],
            'background_image' => $validated['background_image'] ?? null,
            'editable_elements' => $validated['editable_elements'] ?? [],
            'canvas_size' => $validated['canvas_size'] ?? '800x600',
            'design_notes' => $validated['design_notes'] ?? null,
            'version' => $template->version + 1,
            'last_edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ التصميم بنجاح',
            'template' => $template->fresh()
        ]);
    }

    /**
     * Upload and save background image for template.
     */
    public function uploadBackground(Request $request, Template $template)
    {
        $request->validate([
            'background' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        // Delete old background if exists
        if ($template->background_image) {
            Storage::disk('public')->delete($template->background_image);
        }

        // Store new background
        $path = $request->file('background')->store('templates/backgrounds', 'public');

        $template->update([
            'background_image' => $path,
            'last_edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم رفع صورة الخلفية بنجاح',
            'background_url' => asset('storage/' . $path)
        ]);
    }

    /**
     * Generate and save thumbnail for template.
     */
    public function generateThumbnail(Request $request, Template $template)
    {
        $request->validate([
            'thumbnail_data' => 'required|string', // Base64 image data
        ]);

        try {
            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->thumbnail_data));

            // Generate unique filename
            $filename = 'template_' . $template->id . '_' . time() . '.png';
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
                'thumbnail_url' => asset('storage/' . $path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء المعاينة'
            ], 500);
        }
    }


}
