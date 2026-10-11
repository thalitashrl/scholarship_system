<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }
            return redirect()->guest(route('login'));
        }

        if (!$request->user()->roles->contains('name', $role)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden (Role mismatch)'
                ], 403);
            }
            abort(403, 'Akses Ditolak');
        }

        return $next($request);
    }
}
