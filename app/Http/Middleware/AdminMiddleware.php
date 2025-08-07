<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Check if user has Admin or Administration role
        $user = Auth::user();
        $hasAdminRole = $user->roles()->whereIn('name', ['Admin', 'Administration'])->exists();

        if (!$hasAdminRole) {
            abort(403, 'Access denied. Admin role required.');
        }

        return $next($request);
    }
} 