<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Show the compose email form.
     */
    public function create(User $user)
    {
        return view('admin.emails.create', compact('user'));
    }

    /**
     * Send the email and log it.
     */
    public function send(Request $request, User $user)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        // Log the email
        EmailLog::create([
            'user_id' => $user->id,
            'recipient' => $user->email,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'sent_by' => auth()->id(),
            'sent_at' => now(),
        ]);

        // Send the actual mail using Laravel's built-in mailer
        try {
            Mail::raw($validated['body'], function ($message) use ($user, $validated) {
                $message->to($user->email, $user->full_name)
                    ->subject($validated['subject']);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send internal email: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', "Email logged but failed to deliver: {$e->getMessage()}");
        }

        return redirect()->route('admin.users.index')
            ->with('status', "Email sent to {$user->full_name} ({$user->email}) successfully.");
    }

    /**
     * Show email log for a specific user.
     */
    public function history(User $user)
    {
        $emails = EmailLog::where('user_id', $user->id)
            ->latest('sent_at')
            ->get();

        return view('admin.emails.history', compact('user', 'emails'));
    }
}
