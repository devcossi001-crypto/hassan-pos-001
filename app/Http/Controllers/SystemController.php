<?php

namespace App\Http\Controllers;

use App\Models\SystemStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemController extends Controller
{
    /**
     * Show system status dashboard
     */
    public function status(): View
    {
        $systemStatus = SystemStatus::getCurrent();
        
        return view('system.status', [
            'systemStatus' => $systemStatus
        ]);
    }

    /**
     * Activate the system
     */
    public function activate(): RedirectResponse
    {
        $systemStatus = SystemStatus::getCurrent();
        $systemStatus->update([
            'is_active' => true,
            'status_reason' => null,
        ]);

        return redirect()->route('system.status')
            ->with('success', 'System activated successfully.');
    }

    /**
     * Deactivate the system
     */
    public function deactivate(Request $request): RedirectResponse
    {
        $request->validate([
            'status_reason' => 'required|string|max:255',
        ]);

        $systemStatus = SystemStatus::getCurrent();
        $systemStatus->update([
            'is_active' => false,
            'status_reason' => $request->status_reason,
        ]);

        return redirect()->route('system.status')
            ->with('success', 'System deactivated successfully.');
    }

    /**
     * Update subscription end date
     */
    public function updateSubscription(Request $request): RedirectResponse
    {
        $request->validate([
            'subscription_end_date' => 'required|date|after:today',
        ]);

        $systemStatus = SystemStatus::getCurrent();
        $systemStatus->update([
            'subscription_end_date' => $request->subscription_end_date,
            'subscription_active' => true,
        ]);

        return redirect()->route('system.status')
            ->with('success', 'Subscription end date updated successfully.');
    }
}
