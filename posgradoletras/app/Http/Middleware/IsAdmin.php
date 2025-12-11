<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is an admin
        if (!$request->user() || !$request->user()->isAdmin()) {
            // Redirect to home or login with error message
            return redirect('/')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
