<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{

    /**
     * Display a listing of enrollments
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'course.category']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Search by student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->paginate(15);
        $courses = Course::select('id', 'title')->get();

        return view('admin.enrollments.index', compact('enrollments', 'courses'));
    }

    /**
     * Show the form for creating a new enrollment
     */
    public function create()
    {
        $students = User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })->get();

        $courses = Course::where('status', 'published')->get();

        return view('admin.enrollments.create', compact('students', 'courses'));
    }

    /**
     * Store a newly created enrollment
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:pending,approved,rejected,completed,cancelled',
            'enrollment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check if enrollment already exists
        $existingEnrollment = Enrollment::where('student_id', $request->student_id)
                                       ->where('course_id', $request->course_id)
                                       ->first();

        if ($existingEnrollment) {
            return redirect()->back()->withErrors(['student_id' => 'الطالب مسجل بالفعل في هذه الدورة']);
        }

        Enrollment::create($request->all());

        return redirect()->route('admin.enrollments.index')->with('success', 'تم إضافة التسجيل بنجاح');
    }

    /**
     * Display the specified enrollment
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'course.category', 'course.instructor']);
        return view('admin.enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified enrollment
     */
    public function edit(Enrollment $enrollment)
    {
        $students = User::whereHas('role', function($query) {
            $query->where('name', 'student');
        })->get();

        $courses = Course::select('id', 'title')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courses'));
    }

    /**
     * Update the specified enrollment
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed,cancelled',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|numeric|min:0|max:100',
        ]);

        $enrollment->update($request->all());

        return redirect()->route('admin.enrollments.index')->with('success', 'تم تحديث التسجيل بنجاح');
    }

    /**
     * Remove the specified enrollment
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('admin.enrollments.index')->with('success', 'تم حذف التسجيل بنجاح');
    }

    /**
     * Approve enrollment
     */
    public function approve(Enrollment $enrollment)
    {
        // Check if course has available spots
        if ($enrollment->course->isFull()) {
            return redirect()->back()->with('error', 'الدورة مكتملة العدد');
        }

        $enrollment->approve();
        return redirect()->back()->with('success', 'تم قبول التسجيل بنجاح');
    }

    /**
     * Reject enrollment
     */
    public function reject(Enrollment $enrollment)
    {
        $enrollment->reject();
        return redirect()->back()->with('success', 'تم رفض التسجيل');
    }

    /**
     * Complete enrollment
     */
    public function complete(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'grade' => 'nullable|numeric|min:0|max:100'
        ]);

        $enrollment->complete($request->grade);
        return redirect()->back()->with('success', 'تم إكمال التسجيل بنجاح');
    }
}
