<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

// Public Routes - Student Application
Route::get('/', [ApplicationController::class, 'showForm'])->name('application.form');
Route::get('/apply', [ApplicationController::class, 'showForm'])->name('apply');
Route::post('/application', [ApplicationController::class, 'store'])->name('application.store');
Route::get('/success', [ApplicationController::class, 'success'])->name('application.success');
Route::get('/status/{code}', [ApplicationController::class, 'status'])->name('application.status');

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
    Route::resource('applications', ApplicationController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::post('applications/{application}/register', [ApplicationController::class, 'register'])->name('applications.register');
    Route::post('applications/{application}/waiting', [ApplicationController::class, 'putOnWaitingList'])->name('applications.waiting');
    Route::post('applications/{application}/unregister', [ApplicationController::class, 'unregister'])->name('applications.unregister');
    Route::post('applications/{application}/promote', [ApplicationController::class, 'promoteFromWaiting'])->name('applications.promote');
    Route::post('applications/update-status/{application}', [ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    Route::post('applications/bulk-update', [ApplicationController::class, 'bulkUpdate'])->name('applications.bulkUpdate');
    Route::delete('applications/bulk-delete', [ApplicationController::class, 'bulkDelete'])->name('applications.bulkDelete');
    Route::post('applications/bulk-action', [ApplicationController::class, 'bulkAction'])->name('applications.bulkAction');
        
        // Courses Management
        Route::resource('courses', AdminCourseController::class);
        
        // Categories Management
        Route::resource('categories', AdminCategoryController::class);
    });
});
