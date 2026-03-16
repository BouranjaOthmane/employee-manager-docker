<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($request->is('/')) {
            if ($user->hasRole('admin') || $user->hasRole('hr')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('employee')) {
                return redirect()->route('employee.dashboard');
            }
        }

        return $next($request);
    }
}