<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Routes that should be accessible even when password is expired.
     */
    protected array $allowedRoutes = [
        'password.expired',
        'password.expired.update',
        'logout',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Only applies to active users with an expired password
            if (
                $user->status === 'active' &&
                $user->password_expires_at &&
                now()->greaterThan($user->password_expires_at)
            ) {
                $currentRoute = $request->route()?->getName();

                if (!in_array($currentRoute, $this->allowedRoutes)) {
                    return redirect()->route('password.expired');
                }
            }
        }

        return $next($request);
    }
}
