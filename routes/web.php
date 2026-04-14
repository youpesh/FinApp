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

// Chart of Accounts – admin-only management (must be before wildcard routes)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('accounts/create', [\App\Http\Controllers\AccountController::class, 'create'])->name('accounts.create');
    Route::post('accounts', [\App\Http\Controllers\AccountController::class, 'store'])->name('accounts.store');
    Route::get('accounts/{account}/edit', [\App\Http\Controllers\AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('accounts/{account}', [\App\Http\Controllers\AccountController::class, 'update'])->name('accounts.update');
    Route::patch('accounts/{account}/deactivate', [\App\Http\Controllers\AccountController::class, 'deactivate'])->name('accounts.deactivate');
});

// Chart of Accounts – view access for all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('accounts', [\App\Http\Controllers\AccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/{account}/event-log', [\App\Http\Controllers\AccountController::class, 'eventLog'])->name('accounts.event-log');
    Route::get('accounts/{account}', [\App\Http\Controllers\AccountController::class, 'show'])->name('accounts.show');

    // Journal Entries
    Route::resource('journal-entries', \App\Http\Controllers\JournalEntryController::class)->except(['edit', 'update', 'destroy']);

    // Adjusting Journal Entries (Sprint 4)
    Route::get('adjusting-entries', [\App\Http\Controllers\AdjustingEntryController::class, 'index'])->name('adjusting-entries.index');
    Route::get('adjusting-entries/create', [\App\Http\Controllers\AdjustingEntryController::class, 'create'])->name('adjusting-entries.create');
    Route::post('adjusting-entries', [\App\Http\Controllers\AdjustingEntryController::class, 'store'])->name('adjusting-entries.store');
    Route::get('adjusting-entries/{adjustingEntry}', [\App\Http\Controllers\AdjustingEntryController::class, 'show'])
        ->name('adjusting-entries.show');

    // General Ledger
    Route::get('ledger', [\App\Http\Controllers\LedgerController::class, 'index'])->name('ledger.index');
    Route::get('ledger/{account}', [\App\Http\Controllers\LedgerController::class, 'show'])->name('ledger.show');

    // Financial Reports (Sprint 4)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/trial-balance', [\App\Http\Controllers\ReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/income-statement', [\App\Http\Controllers\ReportController::class, 'incomeStatement'])->name('income-statement');
        Route::get('/balance-sheet', [\App\Http\Controllers\ReportController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('/retained-earnings', [\App\Http\Controllers\ReportController::class, 'retainedEarnings'])->name('retained-earnings');
        Route::post('/save', [\App\Http\Controllers\ReportController::class, 'save'])->name('save');
        Route::post('/email', [\App\Http\Controllers\ReportController::class, 'email'])->name('email');
        Route::get('/pdf/{type}', [\App\Http\Controllers\ReportController::class, 'pdf'])->name('pdf');
        Route::get('/snapshots/{financialReport}', [\App\Http\Controllers\ReportController::class, 'showSnapshot'])->name('snapshot.show');
    });

    // Notifications (Sprint 4)
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');

    // Email from account page (Sprint 4)
    Route::post('accounts/{account}/email', [\App\Http\Controllers\AccountController::class, 'email'])->name('accounts.email');

    // Manager Approval
    Route::middleware('role:manager,admin')->name('manager.')->prefix('manager')->group(function () {
        Route::post('journal-entries/{journalEntry}/approve', [\App\Http\Controllers\ManagerApprovalController::class, 'approve'])->name('journal-entries.approve');
        Route::post('journal-entries/{journalEntry}/reject', [\App\Http\Controllers\ManagerApprovalController::class, 'reject'])->name('journal-entries.reject');
        Route::post('adjusting-entries/{journalEntry}/approve', [\App\Http\Controllers\ManagerApprovalController::class, 'approve'])->name('adjusting-entries.approve');
        Route::post('adjusting-entries/{journalEntry}/reject', [\App\Http\Controllers\ManagerApprovalController::class, 'reject'])->name('adjusting-entries.reject');
    });
});

require __DIR__ . '/auth.php';
