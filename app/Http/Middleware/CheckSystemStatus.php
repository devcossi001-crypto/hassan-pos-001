<?php

namespace App\Http\Middleware;

use App\Models\SystemStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to authentication routes regardless of system status
        $publicRoutes = [
            'login',
            'register',
            'password.request',
            'password.reset',
            'password.email',
            'password.update',
            'logout',
            'system.inactive',
        ];

        if ($request->routeIs(...$publicRoutes)) {
            return $next($request);
        }

        $systemStatus = SystemStatus::getCurrent();

        // Allow owner to access system management pages regardless of status
        if (auth()->check() && auth()->user()->isOwner()) {
            return $next($request);
        }

        // Check if system is inactive or subscription expired
        if (!$systemStatus->is_active || !$systemStatus->isSubscriptionValid()) {
            if (auth()->check()) {
                auth()->logout();
            }
            
            return response()->view('system.inactive', [
                'systemStatus' => $systemStatus,
                'reason' => $this->getInactiveReason($systemStatus)
            ], 503);
        }

        return $next($request);
    }

    /**
     * Get the reason for system being inactive
     */
    private function getInactiveReason(SystemStatus $status): string
    {
        if (!$status->is_active) {
            return 'System has been deactivated by the owner.';
        }

        if (!$status->isSubscriptionValid()) {
            return 'Your subscription has expired. Please contact the system owner.';
        }

        return 'System is temporarily unavailable.';
    }
}

