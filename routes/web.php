<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

// Public Routes - Student Application
Route::get('/', [ApplicationController::class, 'showForm'])->name('applications.form');
Route::get('/apply', [ApplicationController::class, 'showForm'])->name('apply');
Route::get('/application-form', [ApplicationController::class, 'showForm'])->name('applications.form.alternative');
Route::get('/applications', [ApplicationController::class, 'showForm'])->name('applications.index');
Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
Route::get('/success', [ApplicationController::class, 'success'])->name('applications.success');
Route::get('/status/{code}', [ApplicationController::class, 'status'])->name('applications.status');

// Redirect old /application path to prevent errors
Route::redirect('/application', '/applications');

Route::get('/courses/{course:slug}', [\App\Http\Controllers\HomeController::class, 'showCourse'])->name('courses.show');

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login/register forms)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware('auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        }); // Redirect admin root to dashboard
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Admin applications management
        Route::resource('applications', AdminApplicationController::class);
        Route::get('applications-export', [AdminApplicationController::class, 'export'])->name('applications.export');
        Route::patch('applications/{application}/update-courses', [AdminApplicationController::class, 'updateCourses'])->name('applications.update-courses');
        Route::post('applications/{application}/register', [AdminApplicationController::class, 'register'])->name('applications.register');
        Route::post('applications/{application}/waiting', [AdminApplicationController::class, 'putOnWaitingList'])->name('applications.waiting');
        Route::post('applications/{application}/unregister', [AdminApplicationController::class, 'unregister'])->name('applications.unregister');
        Route::post('applications/{application}/promote', [AdminApplicationController::class, 'promoteFromWaiting'])->name('applications.promote');
        Route::post('applications/{application}/update-status', [AdminApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
        Route::post('applications/bulk-update', [AdminApplicationController::class, 'bulkUpdate'])->name('applications.bulk-update');
        Route::delete('applications/bulk-delete', [AdminApplicationController::class, 'bulkDelete'])->name('applications.bulk-delete');
        Route::post('applications/bulk-action', [AdminApplicationController::class, 'bulkAction'])->name('applications.bulk-action');

        // Courses Management
        Route::resource('courses', AdminCourseController::class);

        // Categories Management
        Route::resource('categories', AdminCategoryController::class);

        // Enrollments Management
        Route::resource('enrollments', \App\Http\Controllers\Admin\EnrollmentController::class);

        // Admin Management Routes
        Route::get('/profile', [\App\Http\Controllers\Admin\AdminController::class, 'showProfile'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Admin\AdminController::class, 'updateProfile'])->name('profile.update');
        Route::get('/admins/create', [\App\Http\Controllers\Admin\AdminController::class, 'showCreateAdmin'])->name('admins.create');
        Route::post('/admins', [\App\Http\Controllers\Admin\AdminController::class, 'storeAdmin'])->name('admins.store');
    });
});
