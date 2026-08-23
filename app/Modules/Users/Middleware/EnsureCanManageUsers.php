<?php

namespace App\Modules\Users\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanManageUsers
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->canManageUsers(), 403);

        return $next($request);
    }
}
