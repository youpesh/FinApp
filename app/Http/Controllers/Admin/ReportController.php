<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Display a report of all users in the system.
     */
    public function users()
    {
        $users = User::withTrashed()
            ->orderBy('status')
            ->orderBy('last_name')
            ->paginate(20);

        return view('admin.reports.users', compact('users'));
    }

    /**
     * Display a report of users with expired passwords.
     */
    public function expiredPasswords()
    {
        $users = User::whereNotNull('password_expires_at')
            ->where('password_expires_at', '<', now())
            ->where('status', '!=', 'inactive')
            ->orderBy('password_expires_at')
            ->get();

        return view('admin.reports.expired-passwords', compact('users'));
    }
}
