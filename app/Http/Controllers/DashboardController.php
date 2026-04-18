<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\User;
use App\Models\UserAccessRequest;
use App\Services\FinancialRatioService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(FinancialRatioService $ratios)
    {
        $user = Auth::user();

        return view('dashboard', [
            'ratios' => $ratios->compute(),
            'alerts' => $this->alertsFor($user),
        ]);
    }

    /**
     * Build a short, role-scoped list of action items shown at the top of the
     * dashboard. Each item: [label, count, url, severity].
     */
    private function alertsFor($user): array
    {
        $alerts = [];

        if ($user->isAdmin()) {
            $pendingRequests = UserAccessRequest::where('status', 'pending')->count();
            $alerts[] = [
                'label' => 'Pending access requests',
                'count' => $pendingRequests,
                'url' => route('admin.requests.index'),
                'severity' => $pendingRequests > 0 ? 'warn' : 'ok',
            ];

            $expiredPasswords = User::whereNotNull('password_expires_at')
                ->where('password_expires_at', '<', now())
                ->where('status', '!=', 'inactive')
                ->count();
            $alerts[] = [
                'label' => 'Users with expired passwords',
                'count' => $expiredPasswords,
                'url' => route('admin.reports.expired-passwords'),
                'severity' => $expiredPasswords > 0 ? 'warn' : 'ok',
            ];
        }

        if ($user->isManager() || $user->isAdmin()) {
            $pendingJE = JournalEntry::where('status', 'pending')->regular()->count();
            $alerts[] = [
                'label' => 'Journal entries awaiting approval',
                'count' => $pendingJE,
                'url' => route('journal-entries.index', ['status' => 'pending']),
                'severity' => $pendingJE > 0 ? 'warn' : 'ok',
            ];

            $pendingAJE = JournalEntry::where('status', 'pending')->adjusting()->count();
            $alerts[] = [
                'label' => 'Adjusting entries awaiting approval',
                'count' => $pendingAJE,
                'url' => route('adjusting-entries.index', ['status' => 'pending']),
                'severity' => $pendingAJE > 0 ? 'warn' : 'ok',
            ];
        }

        if ($user->isAccountant()) {
            $myRejected = JournalEntry::where('status', 'rejected')
                ->where('created_by', $user->id)
                ->count();
            $alerts[] = [
                'label' => 'Your rejected entries to revisit',
                'count' => $myRejected,
                'url' => route('journal-entries.index', ['status' => 'rejected']),
                'severity' => $myRejected > 0 ? 'warn' : 'ok',
            ];
        }

        return $alerts;
    }
}
