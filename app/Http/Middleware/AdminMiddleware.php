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
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user logged in
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Get user
        $user = Auth::user();

        // Check admin role
        if ($user->role !== 'admin') {
            return redirect('/');
        }

        return $next($request);
    }
}