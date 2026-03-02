<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request, PasswordService $passwordService): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', new \App\Rules\StrongPassword, 'confirmed'],
        ]);

        $user = $request->user();

        if ($passwordService->wasPasswordUsedBefore($user, $validated['password'])) {
            throw ValidationException::withMessages([
                'password' => 'This password has been used before. Please choose a different password.',
            ])->errorBag('updatePassword');
        }

        $user->update([
            'password' => $validated['password'],
            'password_expires_at' => now()->addDays(90),
        ]);

        $passwordService->saveToHistory($user, $user->password);

        return back()->with('status', 'password-updated');
    }
}
