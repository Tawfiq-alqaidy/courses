<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Application;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application homepage
     */
    public function index()
    {
        $categories = Category::withCount('courses')->get();
        $totalCourses = Course::count();
        $totalApplications = Application::count();
        $availableCourses = Course::where('start_time', '>', now())->count();

        return view('dashboard', compact('categories', 'totalCourses', 'totalApplications', 'availableCourses'));
    }

    /**
     * Show courses page
     */
    public function courses(Request $request)
    {
        $query = Course::with(['category']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by availability
        if ($request->filled('available_only')) {
            $query->where('start_time', '>', now());
        }

        $courses = $query->orderBy('start_time', 'asc')->paginate(12);
        $categories = Category::all();

        return view('courses.index', compact('courses', 'categories'));
    }

    /**
     * Show course details
     */
    public function courseDetails(Course $course)
    {
        $course->load(['category']);

        $relatedCourses = Course::where('category_id', $course->category_id)
                               ->where('id', '!=', $course->id)
                               ->take(4)
                               ->get();

        return view('courses.show', compact('course', 'relatedCourses'));
    }

    /**
     * Show categories page
     */
    public function categories()
    {
        $categories = Category::withCount(['courses' => function($query) {
            $query->where('start_time', '>', now());
        }])->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show category details
     */
    public function categoryDetails(Category $category)
    {
        $category->load(['courses' => function($query) {
            $query->where('start_time', '>', now())
                  ->orderBy('start_time', 'asc');
        }]);

        return view('categories.show', compact('category'));
    }
}
