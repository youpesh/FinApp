<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\StrongPassword;
use App\Services\PasswordService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SecurityQuestionResetController extends Controller
{
    /**
     * Step 1: Show form to enter email and username.
     */
    public function showIdentifyForm(): View
    {
        return view('auth.security-reset.identify');
    }

    /**
     * Step 2: Verify identity and show security question.
     */
    public function verifyIdentity(Request $request): View|RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'username' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)
            ->where('username', $request->username)
            ->first();

        if (!$user || !$user->security_question) {
            // Don't reveal whether user exists
            return back()->withErrors([
                'email' => 'We could not find an account matching those credentials, or no security question is set.',
            ])->withInput();
        }

        // Store user id in session for the next step
        session(['security_reset_user_id' => $user->id]);

        return view('auth.security-reset.answer', [
            'security_question' => $user->security_question,
        ]);
    }

    /**
     * Step 3: Verify security answer and show new password form.
     */
    public function verifyAnswer(Request $request): View|RedirectResponse
    {
        $request->validate([
            'security_answer' => ['required', 'string'],
        ]);

        $userId = session('security_reset_user_id');

        if (!$userId) {
            return redirect()->route('password.security.identify')
                ->withErrors(['email' => 'Session expired. Please start over.']);
        }

        $user = User::findOrFail($userId);

        if (!Hash::check(strtolower(trim($request->security_answer)), $user->security_answer)) {
            return back()->withErrors([
                'security_answer' => 'The security answer is incorrect.',
            ]);
        }

        // Mark that the answer was verified
        session(['security_answer_verified' => true]);

        return view('auth.security-reset.reset');
    }

    /**
     * Step 4: Reset the password.
     */
    public function resetPassword(Request $request, PasswordService $passwordService): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', new StrongPassword],
        ]);

        $userId = session('security_reset_user_id');
        $verified = session('security_answer_verified');

        if (!$userId || !$verified) {
            return redirect()->route('password.security.identify')
                ->withErrors(['email' => 'Session expired. Please start over.']);
        }

        $user = User::findOrFail($userId);

        // Check password history
        if ($passwordService->wasPasswordUsedBefore($user, $request->password)) {
            throw ValidationException::withMessages([
                'password' => 'This password has been used before. Please choose a different password.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
            'password_expires_at' => now()->addDays(90),
            'failed_login_attempts' => 0,
        ])->save();

        $passwordService->saveToHistory($user, $user->password);

        // Clean up session
        session()->forget(['security_reset_user_id', 'security_answer_verified']);

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. Please log in with your new password.');
    }
}
