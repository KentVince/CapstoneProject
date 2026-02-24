<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPanelUserToWelcome
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to /admin routes
        if (!$request->is('admin*') || $request->is('admin/login') || $request->is('admin/register') || $request->is('admin/logout')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (auth()->check()) {
            $user = auth()->user();

            // If user is a panel_user and not on the welcome page, redirect them
            if ($user->hasRole('panel_user') && !$request->is('admin/welcome')) {
                return redirect('/admin/welcome');
            }
        }

        return $next($request);
    }
}
