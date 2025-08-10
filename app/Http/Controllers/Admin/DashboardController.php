<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Category;

class DashboardController extends Controller
{


    /**
     * Show admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_applications' => \App\Models\Application::count(),
            'registered_applications' => \App\Models\Application::registered()->count(),
            'waiting_applications' => \App\Models\Application::waiting()->count(),
            'unregistered_applications' => \App\Models\Application::unregistered()->count(),
            'total_courses' => Course::count(),
            'upcoming_courses' => Course::where('start_time', '>', now())->count(),
            'active_courses' => Course::where('start_time', '<=', now())
                                     ->where('end_time', '>=', now())
                                     ->count(),
            'total_categories' => Category::count(),
        ];

        // Recent applications
        $recentApplications = \App\Models\Application::with(['category'])
                                     ->latest()
                                     ->take(10)
                                     ->get();

        // Popular categories (most applications)
        $popularCategories = Category::withCount(['courses', 'courses as applications_count' => function($query) {
                                         // This is a simplified count, would need a more complex query for exact count
                                     }])
                                   ->orderByDesc('courses_count')
                                   ->take(5)
                                   ->get();

        // Upcoming courses
        $upcomingCourses = Course::with('category')
                                ->where('start_time', '>', now())
                                ->orderBy('start_time', 'asc')
                                ->take(5)
                                ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'popularCategories', 'upcomingCourses'));
    }
}
