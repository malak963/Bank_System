<?php

namespace App\Http\Middleware;

use App\Modules\Users\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotCustomer
{
    /**
     * Allowed path patterns for customers.
     */
    protected array $allowedPatterns = [
        'portal*',
        '*/portal*',
        'login*',
        '*/login*',
        'logout*',
        '*/logout*',
        'register*',
        '*/register*',
        'password*',
        '*/password*',
        'profile*',
        '*/profile*',
        'user/two-factor-challenge*',
        '*/user/two-factor-challenge*',
        'api*',
        'up',
    ];

    /**
     * Handle an incoming request.
     * Ensure customers cannot access administration and staff dashboards or modules.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->role === UserRole::Customer || (is_string($user->role) && $user->role === 'customer') || $user->role?->value === 'customer')) {
            // Check if current route is within allowed customer patterns
            foreach ($this->allowedPatterns as $pattern) {
                if ($request->is($pattern)) {
                    return $next($request);
                }
            }

            // Customer is strictly prohibited from accessing admin dashboard routes and staff modules!
            return redirect()->route('portal.dashboard');
        }

        return $next($request);
    }
}
