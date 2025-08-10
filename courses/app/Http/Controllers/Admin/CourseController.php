<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        $query = Course::with(['category']);

        // Filter by search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $now = now();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('start_time', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('start_time', '<=', $now)
                          ->where(function($q) use ($now) {
                              $q->whereNull('end_time')
                                ->orWhere('end_time', '>', $now);
                          });
                    break;
                case 'ended':
                    $query->where('end_time', '<=', $now);
                    break;
            }
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $query->orderBy($sortField, 'desc');

        $courses = $query->paginate(15);
        $categories = Category::all();

        return view('admin.courses.index', compact('courses', 'categories'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'capacity_limit' => 'required|integer|min:1',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time'
        ], [
            'title.required' => 'عنوان الدورة مطلوب',
            'description.required' => 'وصف الدورة مطلوب',
            'category_id.required' => 'الفئة مطلوبة',
            'capacity_limit.required' => 'العدد الأقصى مطلوب',
            'capacity_limit.min' => 'العدد الأقصى يجب أن يكون 1 على الأقل',
            'start_time.required' => 'وقت البداية مطلوب',
            'start_time.after' => 'وقت البداية يجب أن يكون في المستقبل',
            'end_time.required' => 'وقت النهاية مطلوب',
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية'
        ]);

        Course::create($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'تم إنشاء الدورة بنجاح');
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        $course->load(['category']);
        
        // Get applications for this course
        $applications = \App\Models\Application::whereJsonContains('selected_courses', $course->id)
                                             ->with('category')
                                             ->latest()
                                             ->paginate(10);

        return view('admin.courses.show', compact('course', 'applications'));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'capacity_limit' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ], [
            'title.required' => 'عنوان الدورة مطلوب',
            'description.required' => 'وصف الدورة مطلوب',
            'category_id.required' => 'الفئة مطلوبة',
            'capacity_limit.required' => 'العدد الأقصى مطلوب',
            'capacity_limit.min' => 'العدد الأقصى يجب أن يكون 1 على الأقل',
            'start_time.required' => 'وقت البداية مطلوب',
            'end_time.required' => 'وقت النهاية مطلوب',
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية'
        ]);

        $course->update($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'تم تحديث الدورة بنجاح');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        // Check if course has applications
        $applicationsCount = \App\Models\Application::whereJsonContains('selected_courses', $course->id)->count();
        
        if ($applicationsCount > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف الدورة لوجود طلبات مرتبطة بها');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'تم حذف الدورة بنجاح');
    }
}
