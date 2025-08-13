<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Category;
use App\Models\Course;
use App\Exports\ApplicationsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;




class ApplicationController extends Controller
{

    /**
     * Display a listing of applications
     */
    public function index(Request $request)
    {
        $query = Application::query()
            ->with(['category', 'student']) // Eager load relationships to avoid N+1 queries
            ->latest();

        // Store base query for statistics
        $baseQuery = clone $query;

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
            $baseQuery->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
            $baseQuery->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $searchCondition = function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_email', 'like', "%{$search}%")
                    ->orWhere('student_phone', 'like', "%{$search}%")
                    ->orWhere('unique_student_code', 'like', "%{$search}%");
            };
            $query->where($searchCondition);
            $baseQuery->where($searchCondition);
        }

        // Apply sorting
        if ($request->get('sort') === 'student_name') {
            $query->orderBy('student_name');
        } elseif ($request->get('sort') === 'status') {
            $query->orderBy('status');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Server-side pagination with 10 records per page
        $applications = $query->paginate(5);

        // Preload all courses needed for the current page to avoid N+1 queries
        $this->preloadCoursesForApplications($applications);

        // Calculate statistics for all filtered records (not just current page)
        $stats = [
            'total' => $baseQuery->count(),
            'unregistered' => (clone $baseQuery)->where('status', 'unregistered')->count(),
            'registered' => (clone $baseQuery)->where('status', 'registered')->count(),
            'waiting' => (clone $baseQuery)->where('status', 'waiting')->count(),
        ];

        // Get categories for filter dropdown
        $categories = Category::all();

        return view('admin.applications.index', compact('applications', 'categories', 'stats'));
    }

    /**
     * Preload courses for applications to avoid N+1 queries
     */
    private function preloadCoursesForApplications($applications)
    {
        // Get all course IDs from all applications
        $courseIds = $applications->pluck('selected_courses')
            ->filter()
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        if (empty($courseIds)) {
            return;
        }

        // Load all courses at once
        $courses = Course::whereIn('id', $courseIds)->get()->keyBy('id');

        // Attach courses to each application
        foreach ($applications as $application) {
            if (!empty($application->selected_courses) && is_array($application->selected_courses)) {
                $application->loadedCourses = collect($application->selected_courses)
                    ->map(function ($courseId) use ($courses) {
                        return $courses->get($courseId);
                    })
                    ->filter(); // Remove null courses (deleted ones)
            } else {
                $application->loadedCourses = collect();
            }
        }
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
            'category_id' => 'required',
            'selected_courses' => 'required|array|min:1',
            'selected_courses.*' => 'exists:courses,id',
            'status' => 'required|in:unregistered,registered,waiting'
        ]);

        // If 'all' is selected, skip category validation
        if ($validated['category_id'] !== 'all') {
            // Verify selected courses belong to the selected category
            $selectedCourses = Course::whereIn('id', $validated['selected_courses'])
                ->where('category_id', $validated['category_id'])
                ->pluck('id')
                ->toArray();

            if (count($selectedCourses) !== count($validated['selected_courses'])) {
                return back()->withErrors(['selected_courses' => 'الدورات المختارة لا تنتمي إلى الفئة المحددة']);
            }
        } else {
            $selectedCourses = $validated['selected_courses'];
        }

        $application->update([
            'student_name' => $validated['student_name'],
            'student_phone' => $validated['student_phone'],
            'category_id' => $validated['category_id'] === 'all' ? null : $validated['category_id'],
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
     * Register application with automatic waiting list logic
     */
    public function register(Application $application)
    {
        $result = $application->register();

        if ($result) {
            return redirect()->back()->with('success', 'تم تسجيل الطلب بنجاح');
        } else {
            return redirect()->back()->with('info', 'تم إضافة الطلب إلى قائمة الانتظار (الدورات ممتلئة)');
        }
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
     * Mark application as unregistered and promote waiting students
     */
    public function unregister(Application $application)
    {
        $application->update(['status' => 'unregistered']);
        return redirect()->back()->with('success', 'تم إلغاء تسجيل الطلب وترقية طلاب قائمة الانتظار تلقائياً');
    }

    /**
     * Attempt to promote application from waiting list
     */
    public function promoteFromWaiting(Application $application)
    {
        if ($application->status !== 'waiting') {
            return response()->json([
                'success' => false,
                'message' => 'الطلب ليس في قائمة الانتظار'
            ]);
        }

        $result = $application->attemptRegistrationFromWaiting();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الطلب من قائمة الانتظار بنجاح',
                'status' => 'registered'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد أماكن متاحة حالياً في الدورات المختارة'
            ]);
        }
    }

    /**
     * Update application status via AJAX
     */
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:unregistered,registered,waiting'
        ]);

        $oldStatus = $application->status;

        switch ($request->status) {
            case 'registered':
                $result = $application->register();
                $message = $result ? 'تم تسجيل الطلب بنجاح' : 'تم إضافة الطلب إلى قائمة الانتظار (الدورات ممتلئة)';
                break;

            case 'waiting':
                $application->putOnWaitingList();
                $message = 'تم وضع الطلب في قائمة الانتظار';
                break;

            case 'unregistered':
                $application->markAsUnregistered();
                $message = 'تم إلغاء تسجيل الطلب وترقية طلاب قائمة الانتظار تلقائياً';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب بنجاح'
        ]);
    }

    /**
     * Bulk update applications
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:applications,id',
            'status' => 'required|in:registered,waiting'
        ]);

        $applications = Application::whereIn('id', $request->ids)->get();
        $successCount = 0;

        foreach ($applications as $application) {
            switch ($request->status) {
                case 'registered':
                    if ($application->canBeRegistered()) {
                        $application->register();
                        $successCount++;
                    }
                    break;

                case 'waiting':
                    $application->putOnWaitingList();
                    $successCount++;
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم تحديث {$successCount} طلب بنجاح"
        ]);
    }

    /**
     * Bulk delete applications
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:applications,id'
        ]);

        $count = Application::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$count} طلب بنجاح"
        ]);
    }

    /**
     * Bulk actions for backward compatibility
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

    /**
     * Export applications to Excel (.xlsx) format only
     * No alerts, immediate download, handles thousands of records efficiently
     */
    public function export(Request $request)
    {
        try {
            return Excel::download(new ApplicationsExport($request), 'applications.xlsx');
        } catch (\Exception $e) {
            Log::error('Excel export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تصدير البيانات. الرجاء المحاولة مرة أخرى.');
        }
    }
}
