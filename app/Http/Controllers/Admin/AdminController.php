<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            function ($request, $next) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                if (!Auth::check() || !$user->isAdmin()) {
                    abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
                }
                return $next($request);
            }
        ];
    }

    /**
     * Show admin profile edit form
     */
    public function showProfile()
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($admin->id)],
        ];

        // Only validate password if it's being updated
        if ($request->filled('password')) {
            $rules['password'] = [
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                'confirmed'
            ];
        }

        $request->validate($rules, [
            'name.required' => 'الاسم الكامل مطلوب',
            'name.max' => 'الاسم الكامل يجب ألا يتجاوز 255 حرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم من قبل مدير آخر',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وصغير ورقم ورمز خاص',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return redirect()->route('admin.profile.edit')->with('success', 'تم تحديث ملف التعريف الشخصي بنجاح');
    }

    /**
     * Show form to create new admin
     */
    public function showCreateAdmin()
    {
        return view('admin.admins.create');
    }

    /**
     * Store new admin
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                'confirmed'
            ],
        ], [
            'name.required' => 'الاسم الكامل مطلوب',
            'name.max' => 'الاسم الكامل يجب ألا يتجاوز 255 حرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وصغير ورقم ورمز خاص',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            return redirect()->back()->with('error', 'خطأ: لم يتم العثور على دور المدير في النظام');
        }

        // Create new admin with admin role
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'تم إنشاء حساب المدير الجديد بنجاح: ' . $admin->name);
    }
}
