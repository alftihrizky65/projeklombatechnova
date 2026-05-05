<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\AiMonitorController;
use App\Http\Controllers\SystemLogController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Auth
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Dashboard (admin + content_manager)
Route::middleware(\App\Http\Middleware\DashboardMiddleware::class)->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('admin.reports');
    Route::get('/certificate/{classId}', [DashboardController::class, 'viewCertificate'])->name('admin.certificate');

    // Chart data APIs
    Route::get('/api/user-growth', [DashboardController::class, 'userGrowthData']);
    Route::get('/api/ai-accuracy', [DashboardController::class, 'aiAccuracyData']);
    Route::get('/api/system-health', [DashboardController::class, 'systemHealth']);

    // Content Management
    Route::get('/content/tutorials', [ContentController::class, 'tutorials'])->name('admin.content.tutorials');
    Route::get('/content/quizzes', [ContentController::class, 'quizzes'])->name('admin.content.quizzes');
    Route::get('/content/quizzes/{quiz}/questions', [ContentController::class, 'manageQuestions'])->name('admin.quizzes.questions');
    Route::post('/content/quizzes/{quiz}/questions', [ContentController::class, 'storeQuestion']);
    Route::delete('/content/questions/{question}', [ContentController::class, 'destroyQuestion']);
    Route::get('/content/dictionary', [ContentController::class, 'dictionary'])->name('admin.content.dictionary');
    Route::post('/content/dictionary', [ContentController::class, 'storeDictionary']);
    Route::put('/content/dictionary/{entry}', [ContentController::class, 'updateDictionary']);
    Route::delete('/content/dictionary/{entry}', [ContentController::class, 'destroyDictionary']);

    Route::get('/quizzes/{id}', [ContentController::class, 'playQuiz']);
    Route::post('/quizzes/{id}/submit', [ContentController::class, 'submitQuiz']);
    
    Route::get('/content/tutorials', [ContentController::class, 'tutorials']);
    Route::put('/content/quizzes/{quiz}', [ContentController::class, 'updateQuiz']);
    Route::delete('/content/quizzes/{quiz}', [ContentController::class, 'destroyQuiz']);

    // AI Monitor
    Route::get('/ai-monitor', [AiMonitorController::class, 'index'])->name('admin.ai-monitor');

    // System Logs (admin only)
    Route::get('/system-logs', [SystemLogController::class, 'index'])
        ->middleware(\App\Http\Middleware\AdminMiddleware::class)
        ->name('admin.system-logs');

    // User Management (admin only)
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
    });
});
