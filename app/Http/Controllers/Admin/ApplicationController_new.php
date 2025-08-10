<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{


    /**
     * Display a listing of applications
     */
    public function index(Request $request)
    {
        $query = Application::with(['category']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('student_email', 'like', "%{$search}%")
                  ->orWhere('unique_student_code', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $applications = $query->latest()->paginate(15);
        $categories = Category::all();

        // Statistics
        $stats = [
            'total' => Application::count(),
            'registered' => Application::registered()->count(),
            'waiting' => Application::waiting()->count(),
            'unregistered' => Application::unregistered()->count()
        ];

        return view('admin.applications.index', compact('applications', 'categories', 'stats'));
    }

    /**
     * Display the specified application
     */
    public function show(Application $application)
    {
        $application->load(['category']);
        $selectedCourses = $application->courses();

        return view('admin.applications.show', compact('application', 'selectedCourses'));
    }

    /**
     * Show the form for editing the application
     */
    public function edit(Application $application)
    {
        $categories = Category::with('courses')->get();
        return view('admin.applications.edit', compact('application', 'categories'));
    }

    /**
     * Update the specified application
     */
    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'student_phone' => 'required|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'selected_courses' => 'required|array|min:1',
            'selected_courses.*' => 'exists:courses,id',
            'status' => 'required|in:unregistered,registered,waiting'
        ]);

        // Verify selected courses belong to the selected category
        $selectedCourses = Course::whereIn('id', $validated['selected_courses'])
                                ->where('category_id', $validated['category_id'])
                                ->pluck('id')
                                ->toArray();

        if (count($selectedCourses) !== count($validated['selected_courses'])) {
            return back()->withErrors(['selected_courses' => 'الدورات المختارة لا تنتمي إلى الفئة المحددة']);
        }

        $application->update([
            'student_name' => $validated['student_name'],
            'student_phone' => $validated['student_phone'],
            'category_id' => $validated['category_id'],
            'selected_courses' => $selectedCourses,
            'status' => $validated['status']
        ]);

        return redirect()->route('admin.applications.index')->with('success', 'تم تحديث الطلب بنجاح');
    }

    /**
     * Remove the specified application
     */
    public function destroy(Application $application)
    {
        $application->delete();

        return redirect()->route('admin.applications.index')
                        ->with('success', 'تم حذف الطلب بنجاح');
    }

    /**
     * Register application
     */
    public function register(Application $application)
    {
        if (!$application->canBeRegistered()) {
            return redirect()->back()->with('error', 'لا يمكن تسجيل هذا الطلب، بعض الدورات مكتملة العدد');
        }

        $application->register();
        return redirect()->back()->with('success', 'تم تسجيل الطلب بنجاح');
    }

    /**
     * Put application on waiting list
     */
    public function putOnWaitingList(Application $application)
    {
        $application->putOnWaitingList();
        return redirect()->back()->with('success', 'تم وضع الطلب في قائمة الانتظار');
    }

    /**
     * Mark application as unregistered
     */
    public function unregister(Application $application)
    {
        $application->markAsUnregistered();
        return redirect()->back()->with('success', 'تم إلغاء تسجيل الطلب');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:register,waiting,unregister,delete',
            'applications' => 'required|array',
            'applications.*' => 'exists:applications,id'
        ]);

        $applications = Application::whereIn('id', $request->applications)->get();
        $count = 0;

        switch ($request->action) {
            case 'register':
                foreach ($applications as $application) {
                    if ($application->canBeRegistered()) {
                        $application->register();
                        $count++;
                    }
                }
                $message = "تم تسجيل {$count} طلب بنجاح";
                break;

            case 'waiting':
                foreach ($applications as $application) {
                    $application->putOnWaitingList();
                    $count++;
                }
                $message = "تم وضع {$count} طلب في قائمة الانتظار";
                break;

            case 'unregister':
                foreach ($applications as $application) {
                    $application->markAsUnregistered();
                    $count++;
                }
                $message = "تم إلغاء تسجيل {$count} طلب";
                break;

            case 'delete':
                foreach ($applications as $application) {
                    $application->delete();
                    $count++;
                }
                $message = "تم حذف {$count} طلب";
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}
