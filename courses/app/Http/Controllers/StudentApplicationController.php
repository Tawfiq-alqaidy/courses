<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\StudentApplication;
use Illuminate\Http\Request;

class StudentApplicationController extends Controller
{
    /**
     * Show application form for a course
     */
    public function create(Course $course)
    {
        // Check if course is available for application
        if ($course->status !== 'published') {
            abort(404, 'الدورة غير متاحة حاليا');
        }

        if ($course->start_date <= now()) {
            return redirect()->route('courses.show', $course->slug)
                           ->with('error', 'انتهت فترة التقديم لهذه الدورة');
        }

        return view('applications.create', compact('course'));
    }

    /**
     * Store a new application
     */
    public function store(Request $request, Course $course)
    {
        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'address' => 'required|string|max:500',
            'education_level' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'current_occupation' => 'nullable|string|max:255',
            'experience_years' => 'required|integer|min:0|max:50',
            'motivation' => 'required|string|min:50|max:1000',
            'expectations' => 'required|string|min:50|max:1000',
            'previous_experience' => 'nullable|string|max:1000',
            'available_times' => 'nullable|array',
            'available_times.*' => 'string'
        ], [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'phone.required' => 'رقم الهاتف مطلوب',
            'date_of_birth.required' => 'تاريخ الميلاد مطلوب',
            'date_of_birth.before' => 'تاريخ الميلاد يجب أن يكون في الماضي',
            'gender.required' => 'الجنس مطلوب',
            'address.required' => 'العنوان مطلوب',
            'education_level.required' => 'المستوى التعليمي مطلوب',
            'experience_years.required' => 'سنوات الخبرة مطلوبة',
            'experience_years.min' => 'سنوات الخبرة يجب أن تكون 0 أو أكثر',
            'motivation.required' => 'الدافع للانضمام مطلوب',
            'motivation.min' => 'الدافع يجب أن يكون 50 حرف على الأقل',
            'expectations.required' => 'التوقعات مطلوبة',
            'expectations.min' => 'التوقعات يجب أن تكون 50 حرف على الأقل'
        ]);

        // Check if user already applied
        $existingApplication = StudentApplication::where('course_id', $course->id)
                                                ->where('email', $validated['email'])
                                                ->first();

        if ($existingApplication) {
            return redirect()->back()->withErrors([
                'email' => 'لقد قمت بالتقديم على هذه الدورة من قبل باستخدام هذا البريد الإلكتروني'
            ])->withInput();
        }

        // Create the application
        $validated['course_id'] = $course->id;
        $validated['amount_to_pay'] = $course->price;

        StudentApplication::create($validated);

        return redirect()->route('applications.success')
                        ->with('course_title', $course->display_title);
    }

    /**
     * Show success page
     */
    public function success()
    {
        if (!session()->has('course_title')) {
            return redirect()->route('home');
        }

        return view('applications.success');
    }
}
