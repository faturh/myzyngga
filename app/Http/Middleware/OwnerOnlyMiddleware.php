<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ($request->user()->role === 'admin' || $request->user()->hasRole('admin'))) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User does not have the right roles.',
                'status' => 403
            ], 403);
        }

        abort(403, 'User does not have the right roles.');
    }
}
