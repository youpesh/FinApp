<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Forced password change for expired passwords
    Route::get('/password/expired', [\App\Http\Controllers\Auth\ExpiredPasswordController::class, 'show'])->name('password.expired');
    Route::put('/password/expired', [\App\Http\Controllers\Auth\ExpiredPasswordController::class, 'update'])->name('password.expired.update');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Access Requests
    Route::get('requests', [\App\Http\Controllers\UserAccessRequestController::class, 'index'])->name('requests.index');
    Route::post('requests/{accessRequest}/approve', [\App\Http\Controllers\UserAccessRequestController::class, 'approve'])->name('requests.approve');
    Route::post('requests/{accessRequest}/deny', [\App\Http\Controllers\UserAccessRequestController::class, 'deny'])->name('requests.deny');

    // Reports
    Route::get('reports/users', [\App\Http\Controllers\Admin\ReportController::class, 'users'])->name('reports.users');
    Route::get('reports/expired-passwords', [\App\Http\Controllers\Admin\ReportController::class, 'expiredPasswords'])->name('reports.expired-passwords');

    // Internal Email
    Route::get('users/{user}/email', [\App\Http\Controllers\Admin\EmailController::class, 'create'])->name('emails.create');
    Route::post('users/{user}/email', [\App\Http\Controllers\Admin\EmailController::class, 'send'])->name('emails.send');
    Route::get('users/{user}/email-history', [\App\Http\Controllers\Admin\EmailController::class, 'history'])->name('emails.history');

    // Activity Logs
    Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
});

require __DIR__ . '/auth.php';
