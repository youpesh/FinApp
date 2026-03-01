<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a paginated listing of all activity logs.
     */
    public function index()
    {
        $logs = Activity::with('causer', 'subject')
            ->latest()
            ->paginate(25);

        return view('admin.activity-logs.index', compact('logs'));
    }
}
