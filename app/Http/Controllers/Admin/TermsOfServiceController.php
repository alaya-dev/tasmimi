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
        // Check if there's already an active terms
        $existingTerms = TermsOfService::where('is_active', true)->first();
        
        if ($existingTerms) {
            return redirect()->route('admin.terms-of-service.edit', $existingTerms)
                ->with('warning', 'يوجد بالفعل اتفاقية استخدام نشطة. يمكنك تعديلها أو إنشاء نسخة جديدة.');
        }

        return Inertia::render('Admin/TermsOfService/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'version' => 'required|string|max:50',
            'effective_date' => 'nullable|date',
        ]);

        // Deactivate all existing terms if creating a new active one
        if ($request->boolean('is_active', true)) {
            TermsOfService::where('is_active', true)->update(['is_active' => false]);
        }

        $terms = TermsOfService::create([
            'title' => $request->title,
            'content' => $request->content,
            'version' => $request->version,
            'effective_date' => $request->effective_date,
            'is_active' => $request->boolean('is_active', true),
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
            'content' => 'required|string',
            'version' => 'required|string|max:50',
            'effective_date' => 'nullable|date',
        ]);

        // If activating this terms, deactivate all others
        if ($request->boolean('is_active') && !$termsOfService->is_active) {
            TermsOfService::where('id', '!=', $termsOfService->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $termsOfService->update([
            'title' => $request->title,
            'content' => $request->content,
            'version' => $request->version,
            'effective_date' => $request->effective_date,
            'is_active' => $request->boolean('is_active'),
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
}
