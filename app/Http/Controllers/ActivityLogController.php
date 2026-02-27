<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        // Security Check: Only Super Admin can view logs
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to Audit Logs.');
        }

        $query = ActivityLog::with('user')->latest();

        // Hide owner user activities from non-owner admins
        if (!auth()->user()->isOwner()) {
            $query->whereDoesntHave('user', function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->where('name', 'owner');
                });
            });
        }

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        $logs = $query->paginate(50);

        return view('admin.activity-logs.index', compact('logs'));
    }

    public function show(ActivityLog $log): View
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.activity-logs.show', compact('log'));
    }
}
