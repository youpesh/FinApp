<?php

namespace App\Services;

use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordService
{
    /**
     * Check if password was used before.
     */
    public function wasPasswordUsedBefore(User $user, string $password): bool
    {
        $histories = $user->passwordHistories()->get();
        
        foreach ($histories as $history) {
            if (Hash::check($password, $history->password_hash)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Save password to history.
     */
    public function saveToHistory(User $user, string $hashedPassword): void
    {
        PasswordHistory::create([
            'user_id' => $user->id,
            'password_hash' => $hashedPassword,
            'created_at' => now(),
        ]);
    }

    /**
     * Set password expiration date (90 days from now).
     */
    public function setPasswordExpiration(User $user): void
    {
        $user->update([
            'password_expires_at' => now()->addDays(90),
        ]);
    }

    /**
     * Check if password is about to expire (within 3 days).
     */
    public function isPasswordExpiringSoon(User $user): bool
    {
        if (!$user->password_expires_at) {
            return false;
        }

        $daysUntilExpiry = now()->diffInDays($user->password_expires_at, false);
        
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 3;
    }

    /**
     * Generate username from name and date.
     * Format: FirstInitial + LastName + MMYY
     */
    public function generateUsername(string $firstName, string $lastName): string
    {
        $firstInitial = strtolower(substr($firstName, 0, 1));
        $lastNameFormatted = strtolower(str_replace(' ', '', $lastName));
        $monthYear = now()->format('my');
        
        $baseUsername = $firstInitial . $lastNameFormatted . $monthYear;
        
        // Check if username exists, if so, append a number
        $username = $baseUsername;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
}
