<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $userRole = auth()->user()->role;

        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'AKSES DITOLAK');
        }

        return $next($request);
    }
}
