<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون على الأقل 6 أحرف',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Check if user exists and has admin role
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'لم يتم العثور على مستخدم بهذا البريد الإلكتروني.',
            ])->withInput();
        }

        // Check if user is active
        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'هذا الحساب غير مفعل. يرجى التواصل مع الإدارة.',
            ])->withInput();
        }

        // Check if user has admin role
        if (!$user->role || $user->role->name !== 'admin') {
            return back()->withErrors([
                'email' => 'ليس لديك صلاحيات للوصول إلى لوحة التحكم.',
            ])->withInput();
        }

        // Attempt to log in
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Update last login time
            $user->update(['last_login_at' => now()]);
            
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'مرحبًا بك ' . $user->name);
        }

        return back()->withErrors([
            'password' => 'كلمة المرور غير صحيحة.',
        ])->withInput();
    }

    public function dashboard(Request $request)
    {
        // Get statistics
        $stats = $this->getDashboardStats();
        
        // Get recent applications
        $recentApplications = \App\Models\Application::with(['category'])
            ->latest()
            ->take(10)
            ->get();

        // Get chart data
        $chartData = $this->getChartData();

        if ($request->wantsJson()) {
            return response()->json([
                'stats' => $stats,
                'recent_applications' => $recentApplications,
                'chart_data' => $chartData
            ]);
        }

        return view('admin-dashboard', compact('stats', 'recentApplications', 'chartData'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }

    private function getDashboardStats()
    {
        $totalApplications = \App\Models\Application::count();
        $pendingApplications = \App\Models\Application::where('status', 'unregistered')->count();
        $approvedApplications = \App\Models\Application::where('status', 'registered')->count();
        $totalCourses = \App\Models\Course::count();
        $totalCategories = \App\Models\Category::count();
        $newApplicationsToday = \App\Models\Application::whereDate('created_at', today())->count();

        $approvalRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0;

        return [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'approved_applications' => $approvedApplications,
            'total_courses' => $totalCourses,
            'total_categories' => $totalCategories,
            'new_applications_today' => $newApplicationsToday,
            'approval_rate' => $approvalRate,
        ];
    }

    private function getChartData()
    {
        // Application status distribution
        $statusCounts = \App\Models\Application::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Applications by category
        $categoryData = \App\Models\Application::join('categories', 'applications.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, COUNT(*) as count')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'unregistered' => $statusCounts->get('unregistered', 0),
            'registered' => $statusCounts->get('registered', 0),
            'waiting' => $statusCounts->get('waiting', 0),
            'category_names' => $categoryData->pluck('name')->toArray(),
            'category_counts' => $categoryData->pluck('count')->toArray(),
        ];
    }
}
