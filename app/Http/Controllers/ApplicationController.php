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
            'category_id' => 'required',
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

        // Generate unique student code starting from #0000001
        $lastApplication = Application::orderBy('id', 'desc')->first();
        $nextNumber = $lastApplication ? $lastApplication->id + 1 : 1;
        $uniqueCode = '#' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        while (Application::where('unique_student_code', $uniqueCode)->exists()) {
            $nextNumber++;
            $uniqueCode = '#' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        }

        // Verify selected courses belong to the selected category, unless 'all' is selected
        if ($validated['category_id'] !== 'all') {
            $selectedCourses = Course::whereIn('id', $validated['selected_courses'])
                ->where('category_id', $validated['category_id'])
                ->pluck('id')
                ->toArray();

            if (count($selectedCourses) !== count($validated['selected_courses'])) {
                return back()->withErrors(['selected_courses' => 'الدورات المختارة لا تنتمي إلى الفئة المحددة'])
                    ->withInput();
            }
        } else {
            $selectedCourses = $validated['selected_courses'];
        }

        // Check for time conflicts between selected courses
        $courses = Course::whereIn('id', $selectedCourses)->get();
        $conflicts = Course::findConflicts($courses);

        if (!empty($conflicts)) {
            $conflictMessages = array_map(function ($conflict) {
                return $conflict['message'];
            }, $conflicts);

            $conflictMessage = 'تعذر المتابعة بسبب تعارض في المواعيد. الدورات التالية لها مواعيد متداخلة:<br><br>';
            $conflictMessage .= '• ' . implode('<br>• ', $conflictMessages);
            $conflictMessage .= '<br><br>يرجى اختيار دورات ذات مواعيد مختلفة لتجنب التعارض.';

            return back()->withErrors(['selected_courses' => $conflictMessage])
                ->withInput();
        }

        // Create application with automatic status determination
        $application = Application::create([
            'student_id' => $user->id,
            'student_name' => $validated['student_name'],
            'student_email' => $validated['student_email'],
            'student_phone' => $validated['student_phone'],
            'category_id' => $validated['category_id'] === 'all' ? null : $validated['category_id'],
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
                    !isset($course1['start_time']) || !isset($course1['end_time']) ||
                    !isset($course2['start_time']) || !isset($course2['end_time']) ||
                    !$course1['start_time'] || !$course1['end_time'] ||
                    !$course2['start_time'] || !$course2['end_time']
                ) {
                    continue;
                }

                try {
                    $start1 = \Carbon\Carbon::parse($course1['start_time']);
                    $end1 = \Carbon\Carbon::parse($course1['end_time']);
                    $start2 = \Carbon\Carbon::parse($course2['start_time']);
                    $end2 = \Carbon\Carbon::parse($course2['end_time']);

                    // Check if courses overlap in time
                    if ($this->timesOverlap($start1, $end1, $start2, $end2)) {
                        $conflict = [
                            'course1' => $course1['title'],
                            'course2' => $course2['title'],
                            'time1' => $start1->format('Y-m-d H:i') . ' - ' . $end1->format('H:i'),
                            'time2' => $start2->format('Y-m-d H:i') . ' - ' . $end2->format('H:i'),
                            'date' => $start1->format('Y-m-d')
                        ];

                        $conflictKey = "'{$conflict['course1']}' ({$conflict['time1']}) و '{$conflict['course2']}' ({$conflict['time2']})";

                        if (!in_array($conflictKey, $conflictingCourses)) {
                            $conflictingCourses[] = $conflictKey;
                        }
                    }
                } catch (\Exception $e) {
                    // Skip invalid date formats
                    continue;
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
        // Check if the dates are the same first (same day conflict)
        if ($start1->isSameDay($start2)) {
            // Two time periods overlap if: start1 < end2 AND start2 < end1
            return $start1->lt($end2) && $start2->lt($end1);
        }

        // Check for multi-day course conflicts
        // Course 1 starts before course 2 ends AND course 2 starts before course 1 ends
        return $start1->lt($end2) && $start2->lt($end1);
    }
}
