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
        $categories = Category::with('courses')->get();
        $courses = Course::with('category')->get();
        
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

        // Verify selected courses belong to the selected category
        $selectedCourses = Course::whereIn('id', $validated['selected_courses'])
                                ->where('category_id', $validated['category_id'])
                                ->pluck('id')
                                ->toArray();

        if (count($selectedCourses) !== count($validated['selected_courses'])) {
            return back()->withErrors(['selected_courses' => 'الدورات المختارة لا تنتمي إلى الفئة المحددة'])
                        ->withInput();
        }

        // Create application with automatic status determination
        $application = Application::create([
            'student_name' => $validated['student_name'],
            'student_email' => $validated['student_email'],
            'student_phone' => $validated['student_phone'],
            'category_id' => $validated['category_id'],
            'selected_courses' => $selectedCourses,
            // Status will be automatically determined in the model
        ]);

        // Provide appropriate feedback based on the assigned status
        if ($application->status === 'registered') {
            $status_message = 'تم تسجيلك بنجاح في الدورات المختارة';
        } else {
            $status_message = 'تم وضعك في قائمة الانتظار للدورات المكتملة العدد. سيتم تسجيلك تلقائياً عند توفر أماكن';
        }

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
            return redirect()->route('applications.create');
        }

        return view('applications.success');
    }

    /**
     * Check application status
     */
    public function status($code)
    {
        $application = Application::where('unique_student_code', $code)
                                 ->with(['category', 'courses'])
                                 ->first();

        if (!$application) {
            abort(404, 'رمز الطالب غير موجود');
        }

        return view('applications.status', compact('application'));
    }
}
