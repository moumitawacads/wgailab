<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        // Not logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Role check
        if (!in_array($user->role, $roles)) {

            // 🔥 Optional: redirect instead of 403
            return match($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'se' => redirect()->route('se.dashboard'),
                'instructor' => redirect()->route('instructor.dashboard'),
                default => abort(403),
            };
        }
        return $next($request);
    }
}
