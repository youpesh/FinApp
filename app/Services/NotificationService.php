<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Notify all active managers. Creates in-app Notification rows and sends email
     * via the existing EmailLog + Mail::raw pattern used in Admin\EmailController.
     *
     * @param  array{type:string,title:string,message:string,action_url?:?string,data?:array}  $payload
     */
    public static function notifyManagers(array $payload, ?int $senderId = null): int
    {
        $managers = User::query()
            ->where(function ($q) {
                $q->where('role', 'manager')->orWhere('role', 'admin');
            })
            ->where('status', 'active')
            ->get();

        foreach ($managers as $manager) {
            self::notifyUser($manager, $payload, $senderId);
        }

        return $managers->count();
    }

    /**
     * Notify a single user.
     */
    public static function notifyUser(User $user, array $payload, ?int $senderId = null): Notification
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $payload['type'],
            'title' => $payload['title'],
            'message' => $payload['message'],
            'action_url' => $payload['action_url'] ?? null,
            'data' => $payload['data'] ?? null,
        ]);

        // Mirror to email via existing audit pattern.
        EmailLog::create([
            'user_id' => $user->id,
            'recipient' => $user->email,
            'subject' => $payload['title'],
            'body' => $payload['message'] . (!empty($payload['action_url']) ? "\n\nLink: " . $payload['action_url'] : ''),
            'sent_by' => $senderId ?? auth()->id() ?? $user->id,
            'sent_at' => now(),
        ]);

        try {
            Mail::raw(
                $payload['message'] . (!empty($payload['action_url']) ? "\n\nLink: " . $payload['action_url'] : ''),
                function ($message) use ($user, $payload) {
                    $message->to($user->email, $user->full_name)
                        ->subject($payload['title']);
                }
            );
        } catch (\Exception $e) {
            Log::error('Notification email failed: ' . $e->getMessage());
        }

        return $notification;
    }
}
