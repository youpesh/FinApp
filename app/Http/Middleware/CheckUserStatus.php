<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Check if user is suspended
            if ($user->status === 'suspended') {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been suspended.']);
            }

            // Check if user is inactive
            if ($user->status === 'inactive') {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been deactivated.']);
            }

            // Check if user is pending approval
            if ($user->status === 'pending') {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account is pending approval.']);
            }

            // Check if password has expired
            if ($user->password_expires_at && now()->greaterThan($user->password_expires_at)) {
                $allowedRoutes = ['profile.edit', 'profile.update', 'password.update', 'logout'];
                if ($request->route() && !in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('profile.edit')->with('status', 'password-expired');
                }
            }
        }

        return $next($request);
    }
}
