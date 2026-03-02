<?php

namespace App\Console\Commands;

use App\Mail\PasswordExpiryWarning;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPasswordExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'passwords:notify-expiring';

    /**
     * The console command description.
     */
    protected $description = 'Send email notifications to users whose passwords will expire within 3 days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users */
        $users = User::where('status', 'active')
            ->whereNotNull('password_expires_at')
            ->where('password_expires_at', '>', now())
            ->where('password_expires_at', '<=', now()->addDays(3))
            ->get();

        $count = 0;

        foreach ($users as $user) {
            $daysRemaining = (int) now()->diffInDays($user->password_expires_at, false);

            if ($daysRemaining >= 0 && $daysRemaining <= 3) {
                Mail::to($user->email)->send(new PasswordExpiryWarning($user, $daysRemaining));
                $count++;
                $this->info("Notification sent to: {$user->email} ({$daysRemaining} days remaining)");
            }
        }

        $this->info("Total notifications sent: {$count}");

        return Command::SUCCESS;
    }
}
