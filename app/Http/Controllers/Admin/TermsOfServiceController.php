<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsOfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class TermsOfServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $terms = TermsOfService::with(['creator', 'updater'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeTerms = TermsOfService::getActive();

        return Inertia::render('Admin/TermsOfService/Index', [
            'terms' => $terms,
            'activeTerms' => $activeTerms,
            'hasTerms' => $terms->isNotEmpty(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/TermsOfService/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'version' => 'nullable|string|max:50',
        ]);

        // Clean and prepare the content
        $content = $this->cleanHtmlContent($request->content);

        TermsOfService::create([
            'title' => $request->title,
            'content' => $content,
            'version' => $request->version ?? '1.0',
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.terms-of-service.index')
            ->with('success', 'تم إنشاء اتفاقية الاستخدام بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TermsOfService $termsOfService): Response
    {
        $termsOfService->load(['creator', 'updater']);

        return Inertia::render('Admin/TermsOfService/Show', [
            'terms' => $termsOfService,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TermsOfService $termsOfService): Response
    {
        return Inertia::render('Admin/TermsOfService/Edit', [
            'terms' => $termsOfService,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TermsOfService $termsOfService): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'version' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        // Clean and prepare the content
        $content = $this->cleanHtmlContent($request->content);

        $termsOfService->update([
            'title' => $request->title,
            'content' => $content,
            'version' => $request->version ?? $termsOfService->version,
            'is_active' => $request->is_active ?? $termsOfService->is_active,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.terms-of-service.index')
            ->with('success', 'تم تحديث اتفاقية الاستخدام بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TermsOfService $termsOfService): RedirectResponse
    {
        $termsOfService->delete();

        return redirect()->route('admin.terms-of-service.index')
            ->with('success', 'تم حذف اتفاقية الاستخدام بنجاح.');
    }

    /**
     * Activate specific terms of service.
     */
    public function activate(TermsOfService $termsOfService): RedirectResponse
    {
        $termsOfService->activate();

        return redirect()->route('admin.terms-of-service.index')
            ->with('success', 'تم تفعيل اتفاقية الاستخدام بنجاح.');
    }

    /**
     * Clean HTML content for safe storage and RTL support
     */
    private function cleanHtmlContent(string $content): string
    {
        // Allow specific HTML tags for rich text with RTL support
        $allowedTags = '<p><br><b><strong><i><em><u><ul><ol><li><div><span><h1><h2><h3><h4><h5><h6><center>';
        $content = strip_tags($content, $allowedTags);
        
        // Ensure RTL direction for Arabic content
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $content)) {
            // Add RTL attributes if not already present
            if (strpos($content, 'dir="rtl"') === false && strpos($content, 'text-align:') === false) {
                // Wrap content in RTL container
                $content = '<div dir="rtl" style="text-align: right; line-height: 1.8;">' . $content . '</div>';
            }
        }
        
        // Clean up extra spaces and formatting
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace(['<p> </p>', '<p></p>'], '', $content);
        
        return trim($content);
    }
}
