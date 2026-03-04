<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Look up user for tracking failed attempts (but do NOT leak their status yet)
        $user = User::where('username', $this->username)->first();

        if (!Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // Increment failed login attempts
            if ($user) {
                $user->increment('failed_login_attempts');
                $user->refresh();

                // Suspend user after 3 failed attempts
                if ($user->failed_login_attempts >= 3) {
                    $user->update([
                        'status' => 'suspended',
                        'suspension_start_date' => now(),
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        // Auth succeeded — NOW check account status (no enumeration leak)
        if ($user->status === 'suspended') {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Your account has been suspended. Please contact an administrator.',
            ]);
        }

        if ($user->status === 'inactive') {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Your account has been deactivated.',
            ]);
        }

        if ($user->status === 'pending') {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Your account is pending administrator approval.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')) . '|' . $this->ip());
    }
}
