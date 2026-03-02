<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\StrongPassword;
use App\Services\PasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExpiredPasswordController extends Controller
{
    /**
     * Show the forced password change form.
     */
    public function show(): View
    {
        return view('auth.password-expired');
    }

    /**
     * Handle the forced password change.
     */
    public function update(Request $request, PasswordService $passwordService): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', new StrongPassword],
        ]);

        $user = Auth::user();

        // Check password history
        if ($passwordService->wasPasswordUsedBefore($user, $request->password)) {
            throw ValidationException::withMessages([
                'password' => 'This password has been used before. Please choose a different password.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_expires_at' => now()->addDays(90),
        ])->save();

        $passwordService->saveToHistory($user, $user->password);

        return redirect()->route('dashboard')->with('status', 'Your password has been updated successfully.');
    }
}
