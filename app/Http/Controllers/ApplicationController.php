<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Show application form
     */
    public function showForm()
    {
        $categories = Category::all();
        $courses = Course::with(['category', 'enrollments'])
            ->where('status', 'active')
            ->get();

        // Load enrollment counts for each course
        foreach ($courses as $course) {
            $course->registered_students_count = $course->getCurrentEnrolledCount();
        }

        return view('application-form', compact('categories', 'courses'));
    }

    /**
     * Store a new application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'student_email' => 'required|email|max:255|unique:applications,student_email',
            'student_phone' => 'required|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'selected_courses' => 'required|array|min:1',
            'selected_courses.*' => 'exists:courses,id'
        ], [
            'student_name.required' => 'اسم الطالب مطلوب',
            'student_email.required' => 'البريد الإلكتروني مطلوب',
            'student_email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'student_email.unique' => 'هذا البريد الإلكتروني مسجل بالفعل',
            'student_phone.required' => 'رقم الهاتف مطلوب',
            'category_id.required' => 'الفئة مطلوبة',
            'selected_courses.required' => 'يجب اختيار دورة واحدة على الأقل',
            'selected_courses.min' => 'يجب اختيار دورة واحدة على الأقل'
        ]);

        // Create or find user
        $user = \App\Models\User::firstOrCreate([
            'email' => $validated['student_email']
        ], [
            'name' => $validated['student_name'],
            'phone' => $validated['student_phone'] ?? null,
            'role' => 'student',
            'password' => bcrypt('temporary123') // User can change later
        ]);

        // Generate unique student code
        $uniqueCode = 'ST' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        while (Application::where('unique_student_code', $uniqueCode)->exists()) {
            $uniqueCode = 'ST' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        // Verify selected courses belong to the selected category
        $selectedCourses = Course::whereIn('id', $validated['selected_courses'])
            ->where('category_id', $validated['category_id'])
            ->pluck('id')
            ->toArray();

        if (count($selectedCourses) !== count($validated['selected_courses'])) {
            return back()->withErrors(['selected_courses' => 'الدورات المختارة لا تنتمي إلى الفئة المحددة'])
                ->withInput();
        }

        // Check for time conflicts between selected courses
        $courses = Course::whereIn('id', $selectedCourses)->get();
        $conflictingCourses = $this->checkTimeConflicts($courses);

        if (!empty($conflictingCourses)) {
            $conflictMessage = 'يوجد تعارض في المواعيد بين الدورات التالية: ' . implode(' و ', $conflictingCourses);
            return back()->withErrors(['selected_courses' => $conflictMessage])
                ->withInput();
        }

        // Create application with automatic status determination
        $application = Application::create([
            'student_id' => $user->id,
            'student_name' => $validated['student_name'],
            'student_email' => $validated['student_email'],
            'student_phone' => $validated['student_phone'],
            'category_id' => $validated['category_id'],
            'selected_courses' => $selectedCourses,
            'unique_student_code' => $uniqueCode,
            // Status will be automatically determined in the model
        ]);

        // Provide feedback for pending application
        $status_message = 'تم استلام طلبك بنجاح. سيتم مراجعة طلبك من قبل الإدارة وسيتم التواصل معك قريباً.';

        return redirect()->route('applications.success')
            ->with([
                'application_code' => $application->unique_student_code,
                'status_message' => $status_message
            ]);
    }

    /**
     * Show success page
     */
    public function success()
    {
        if (!session()->has('application_code')) {
            return redirect()->route('applications.form');
        }

        return view('applications.success');
    }

    /**
     * Check application status
     */
    public function status($code)
    {
        $application = Application::where('unique_student_code', $code)
            ->with(['category'])
            ->first();

        if (!$application) {
            abort(404, 'رمز الطالب غير موجود');
        }

        return view('applications.status', compact('application'));
    }

    /**
     * Check for time conflicts between selected courses
     */
    private function checkTimeConflicts($courses)
    {
        $conflictingCourses = [];
        $coursesArray = $courses->toArray();

        for ($i = 0; $i < count($coursesArray); $i++) {
            for ($j = $i + 1; $j < count($coursesArray); $j++) {
                $course1 = $coursesArray[$i];
                $course2 = $coursesArray[$j];

                // Skip if either course doesn't have start/end times
                if (
                    !$course1['start_time'] || !$course1['end_time'] ||
                    !$course2['start_time'] || !$course2['end_time']
                ) {
                    continue;
                }

                $start1 = \Carbon\Carbon::parse($course1['start_time']);
                $end1 = \Carbon\Carbon::parse($course1['end_time']);
                $start2 = \Carbon\Carbon::parse($course2['start_time']);
                $end2 = \Carbon\Carbon::parse($course2['end_time']);

                // Check if courses overlap in time
                if ($this->timesOverlap($start1, $end1, $start2, $end2)) {
                    $conflictKey = $course1['title'] . ' - ' . $course2['title'];
                    if (!in_array($conflictKey, $conflictingCourses)) {
                        $conflictingCourses[] = $conflictKey;
                    }
                }
            }
        }

        return $conflictingCourses;
    }

    /**
     * Check if two time periods overlap
     */
    private function timesOverlap($start1, $end1, $start2, $end2)
    {
        // Check if the dates are the same first
        if (!$start1->isSameDay($start2)) {
            return false;
        }

        // Check if time periods overlap on the same day
        // Two time periods overlap if: start1 < end2 AND start2 < end1
        return $start1->lt($end2) && $start2->lt($end1);
    }
}
