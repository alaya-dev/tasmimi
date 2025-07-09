<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): Response
    {
        $categories = Category::withCount('templates')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.max' => 'اسم الفئة طويل جداً',
            'name.unique' => 'اسم الفئة موجود بالفعل',
        ]);

        Category::create($validated);

        return back()->with('success', 'تم إنشاء الفئة بنجاح');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): Response
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.max' => 'اسم الفئة طويل جداً',
            'name.unique' => 'اسم الفئة موجود بالفعل',
        ]);

        $category->update($validated);

        return back()->with('success', 'تم تحديث الفئة بنجاح');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has templates
        if ($category->templates()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على قوالب');
        }

        $category->delete();

        return back()->with('success', 'تم حذف الفئة بنجاح');
    }
}
