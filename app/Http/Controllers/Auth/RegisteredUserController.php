<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, \App\Services\PasswordService $passwordService): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', new \App\Rules\StrongPassword],
        ]);

        $username = $passwordService->generateUsername($request->first_name, $request->last_name);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $username,
            'email' => $request->email,
            'password' => $request->password,
            'password_expires_at' => now()->addDays(90),
            // Default role is accountant or we set them to pending?
            // Actually, public registration could default to pending status and no role.
            // But since this is a breeze default, I will set them to pending.
            'status' => 'pending',
        ]);

        $passwordService->saveToHistory($user, $user->password);

        event(new Registered($user));

        // Since they are pending, do not login automatically.
        // Instead, redirect to login with a message.
        return redirect(route('login'))->with('status', 'Registration successful. Your account is pending administrator approval.');
    }
}
