<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminStaffMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->guest(route('login'));
        }

        // Blokir jika mahasiswa mencoba masuk ke area admin/staf
        if ($request->user()->hasRole('mahasiswa')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden (Akses Ditolak)'], 403);
            }
            abort(403, 'Akses Ditolak');
        }

        return $next($request);
    }
}
