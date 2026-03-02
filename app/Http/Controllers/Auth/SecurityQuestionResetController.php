<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityResetToken;
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
     * Step 2: Verify identity, generate token, and show security question.
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
            return back()->withErrors([
                'email' => 'We could not find an account matching those credentials, or no security question is set.',
            ])->withInput();
        }

        // Clean up any old tokens for this user
        SecurityResetToken::where('user_id', $user->id)->delete();

        // Generate a secure token
        $token = Str::random(64);

        SecurityResetToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'answer_verified' => false,
            'expires_at' => now()->addMinutes(15),
        ]);

        return view('auth.security-reset.answer', [
            'security_question' => $user->security_question,
            'token' => $token,
        ]);
    }

    /**
     * Step 3: Verify security answer and show new password form.
     */
    public function verifyAnswer(Request $request): View|RedirectResponse
    {
        $request->validate([
            'security_answer' => ['required', 'string'],
            'token' => ['required', 'string'],
        ]);

        $resetToken = SecurityResetToken::where('token', hash('sha256', $request->token))->first();

        if (!$resetToken || $resetToken->isExpired()) {
            SecurityResetToken::where('token', hash('sha256', $request->token))->delete();
            return redirect()->route('password.security.identify')
                ->withErrors(['email' => 'This reset link has expired. Please start over.']);
        }

        $user = $resetToken->user;

        if (!Hash::check(strtolower(trim($request->security_answer)), $user->security_answer)) {
            return back()->withErrors([
                'security_answer' => 'The security answer is incorrect.',
            ])->with('token', $request->token);
        }

        // Mark token as answer-verified
        $resetToken->update(['answer_verified' => true]);

        return view('auth.security-reset.reset', [
            'token' => $request->token,
        ]);
    }

    /**
     * Step 4: Reset the password.
     */
    public function resetPassword(Request $request, PasswordService $passwordService): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', new StrongPassword],
            'token' => ['required', 'string'],
        ]);

        $resetToken = SecurityResetToken::where('token', hash('sha256', $request->token))->first();

        if (!$resetToken || $resetToken->isExpired() || !$resetToken->answer_verified) {
            return redirect()->route('password.security.identify')
                ->withErrors(['email' => 'This reset link has expired. Please start over.']);
        }

        $user = $resetToken->user;

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

        // Clean up the used token
        $resetToken->delete();

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. Please log in with your new password.');
    }
}
