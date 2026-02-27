<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyOwner
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()?->isOwner()) {
            abort(403, 'Unauthorized. Only system owner can access this.');
        }

        return $next($request);
    }
}
