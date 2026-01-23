<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PasswordExpiryNotification;
use Illuminate\Console\Command;

class NotifyPasswordExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:notify-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users whose passwords are expiring within 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threeDaysFromNow = now()->addDays(3);
        
        $users = User::where('status', 'active')
            ->whereNotNull('password_expires_at')
            ->whereDate('password_expires_at', '<=', $threeDaysFromNow)
            ->whereDate('password_expires_at', '>=', now())
            ->get();

        foreach ($users as $user) {
            $daysUntilExpiry = now()->diffInDays($user->password_expires_at, false);
            $user->notify(new PasswordExpiryNotification($daysUntilExpiry));
        }

        $this->info("Notified {$users->count()} users about password expiry.");
        
        return Command::SUCCESS;
    }
}
