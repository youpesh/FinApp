<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at');

        if ($request->filled('filter') && $request->filter === 'unread') {
            $query->unread();
        }

        $notifications = $query->paginate(25)->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->markRead();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return $notification->action_url
            ? redirect($notification->action_url)
            : back();
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
