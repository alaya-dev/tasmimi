<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsOfService;
use App\Services\FileProcessorService;
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
        // Check if there's already a terms file
        $existingTerms = TermsOfService::first();
        
        if ($existingTerms) {
            return redirect()->route('admin.terms-of-service.edit', $existingTerms)
                ->with('warning', 'يمكن رفع ملف واحد فقط. يمكنك استبدال الملف الموجود.');
        }

        return Inertia::render('Admin/TermsOfService/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FileProcessorService $fileProcessor): RedirectResponse
    {
        // Validate based on input type
        if ($request->hasFile('file')) {
            // Check if there's already a terms file (limit to one file only)
            $existingTerms = TermsOfService::first();
            if ($existingTerms) {
                return back()->withErrors(['file' => 'يمكن رفع ملف واحد فقط. يرجى حذف الملف الموجود أولاً أو استبداله.']);
            }
            
            // File upload mode
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:10240', // 10MB max, PDF seulement
            ]);

            // Process the uploaded file
            $result = $fileProcessor->processFile($request->file('file'));
            
            if (!$result['success']) {
                return back()->withErrors(['file' => $result['error']]);
            }

            // Use filename as title automatically
            $title = $request->title ?? pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);

            $terms = TermsOfService::create([
                'title' => $title,
                'content' => '',
                'content_blocks' => [],
                'file_name' => $result['file_name'],
                'file_path' => $result['file_path'],
                'file_type' => $result['file_type'],
                'file_size' => $result['file_size'],
                'extracted_content' => $result['extracted_content'],
                'is_active' => true, // Always active since only one file allowed
                'created_by' => Auth::id(),
            ]);
        } else {
            // No file provided
            return back()->withErrors(['file' => 'يرجى اختيار ملف PDF للرفع.']);
        }

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
     * Remove the specified resource from storage.
     */
    public function destroy(TermsOfService $termsOfService, FileProcessorService $fileProcessor): RedirectResponse
    {
        // Delete associated file if exists
        if ($termsOfService->hasFile()) {
            $fileProcessor->deleteFile($termsOfService->file_path);
        }
        
        $termsOfService->delete();

        return redirect()->route('admin.terms-of-service.index')
            ->with('success', 'تم حذف اتفاقية الاستخدام بنجاح.');
    }

    /**
     * Download the original file.
     */
    public function downloadFile(TermsOfService $termsOfService)
    {
        if (!$termsOfService->hasFile()) {
            return redirect()->back()->withErrors(['file' => 'لا يوجد ملف مرفق.']);
        }

        $filePath = storage_path('app/public/' . $termsOfService->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->withErrors(['file' => 'الملف غير موجود.']);
        }

        return response()->download($filePath, $termsOfService->file_name);
    }

    /**
     * Delete file and switch to manual mode.
     */
    public function deleteFile(TermsOfService $termsOfService, FileProcessorService $fileProcessor): RedirectResponse
    {
        if (!$termsOfService->hasFile()) {
            return redirect()->back()->withErrors(['file' => 'لا يوجد ملف مرفق.']);
        }

        // Delete file from storage
        $fileProcessor->deleteFile($termsOfService->file_path);

        // Clear file fields but keep extracted content as content_blocks
        $contentBlocks = [];
        if ($termsOfService->extracted_content) {
            $contentBlocks[] = [
                'subtitle' => 'المحتوى المستخرج',
                'content' => $termsOfService->extracted_content
            ];
        }

        $termsOfService->update([
            'file_name' => null,
            'file_path' => null,
            'file_type' => null,
            'file_size' => null,
            'extracted_content' => null,
            'content_blocks' => $contentBlocks,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'تم حذف الملف وتحويل المحتوى إلى النمط اليدوي.');
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
}
