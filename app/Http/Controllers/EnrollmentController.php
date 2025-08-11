<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Enroll student in a course
     */
    public function store(Request $request, Course $course)
    {
        // Check if user is student
        if (!Auth::user()->isStudent()) {
            return redirect()->back()->with('error', 'فقط الطلاب يمكنهم التسجيل في الدورات');
        }

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('student_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()->with('error', 'أنت مسجل بالفعل في هذه الدورة');
        }

        // Check if enrollment is open
        if (!$course->isEnrollmentOpen()) {
            return redirect()->back()->with('error', 'التسجيل غير متاح في هذه الدورة حاليا');
        }

        // Determine enrollment status based on course capacity
        $enrollmentStatus = 'pending';
        $message = 'تم تسجيلك في قائمة الانتظار. سيتم إشعارك عند الموافقة';

        // If course has available spots, approve immediately
        if ($course->hasAvailableSpots()) {
            $enrollmentStatus = 'approved';
            $message = 'تم قبولك في الدورة بنجاح!';
        }

        // Create enrollment
        Enrollment::create([
            'student_id' => Auth::id(),
            'course_id' => $course->id,
            'status' => $enrollmentStatus,
            'enrollment_date' => now(),
            'amount_paid' => $course->price,
        ]);

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show student enrollments
     */
    public function myEnrollments()
    {
        $enrollments = Enrollment::where('student_id', Auth::id())
            ->with(['course.category', 'course.instructor'])
            ->latest()
            ->paginate(10);

        return view('student.enrollments', compact('enrollments'));
    }

    /**
     * Cancel enrollment
     */
    public function cancel(Enrollment $enrollment)
    {
        // Check if user owns this enrollment
        if ($enrollment->student_id !== Auth::id()) {
            abort(403);
        }

        // Only allow cancellation if pending or approved and course hasn't started
        if (
            !in_array($enrollment->status, ['pending', 'approved']) ||
            $enrollment->course->start_date <= now()
        ) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء التسجيل في هذا الوقت');
        }

        $enrollment->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'تم إلغاء التسجيل بنجاح');
    }
}
