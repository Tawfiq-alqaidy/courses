<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'غير مخول للوصول إلى لوحة الإدارة');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::with('courses')->latest()->get();
        
        // Get statistics
        $totalCourses = \App\Models\Course::count();
        $totalApplications = \App\Models\Application::count();
        
        // Get category-specific stats
        $categoryStats = [];
        foreach ($categories as $category) {
            $categoryStats[$category->id] = [
                'applications' => \App\Models\Application::where('category_id', $category->id)->count(),
                'registered' => \App\Models\Application::where('category_id', $category->id)->where('status', 'registered')->count(),
            ];
        }
        
        return view('admin.categories.index', compact('categories', 'totalCourses', 'totalApplications', 'categoryStats'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.unique' => 'هذا الاسم موجود بالفعل'
        ]);

        Category::create($request->only('name'));

        return redirect()->route('admin.categories.index')
                        ->with('success', 'تم إنشاء الفئة بنجاح');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load('courses');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.unique' => 'هذا الاسم موجود بالفعل'
        ]);

        $category->update($request->only('name'));

        return redirect()->route('admin.categories.index')
                        ->with('success', 'تم تحديث الفئة بنجاح');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has courses
        if ($category->courses()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف الفئة لوجود دورات مرتبطة بها');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                        ->with('success', 'تم حذف الفئة بنجاح');
    }
}
