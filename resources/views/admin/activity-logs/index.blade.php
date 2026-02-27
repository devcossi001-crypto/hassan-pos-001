@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>System Activity Trail</h6>
            <div class="ms-auto">
                <form action="{{ route('activity-logs.index') }}" method="GET" class="d-flex gap-2">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-light btn-sm px-3">Reset</a>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0">Timestamp</th>
                            <th class="py-3 border-0">User</th>
                            <th class="py-3 border-0">Action</th>
                            <th class="py-3 border-0">Model</th>
                            <th class="py-3 border-0">Description</th>
                            <th class="py-3 border-0 text-end px-4">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-4 text-muted">
                                <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary-soft text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; background: #e0e7ff;">
                                        {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span>{{ $log->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill 
                                    @if($log->action == 'created') bg-success-soft text-success 
                                    @elseif($log->action == 'updated') bg-info-soft text-info 
                                    @elseif($log->action == 'deleted') bg-danger-soft text-danger 
                                    @else bg-secondary-soft text-secondary @endif"
                                    style="background-opacity: 0.1;">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td><small class="text-uppercase fw-bold text-muted" style="font-size: 10px;">{{ $log->model_type }}</small></td>
                            <td>{{ $log->description }}</td>
                            <td class="text-end px-4">
                                <button type="button" class="btn btn-link btn-sm text-primary p-0" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                    View Changes
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg text-start">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Activity Details #{{ $log->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="small text-muted mb-1">IP Address</label>
                                                        <p class="mb-0 fw-bold">{{ $log->ip_address }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small text-muted mb-1">User Agent</label>
                                                        <p class="mb-0 small text-truncate" title="{{ $log->user_agent }}">{{ $log->user_agent }}</p>
                                                    </div>
                                                </div>
                                                
                                                <label class="small text-muted mb-2">Changes Data</label>
                                                <div class="bg-dark text-light p-3 rounded" style="font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto;">
                                                    <pre class="mb-0">@json($log->changes, JSON_PRETTY_PRINT)</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No activities recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .bg-success-soft { background-color: #ecfdf5; color: #059669; }
    .bg-info-soft { background-color: #eff6ff; color: #2563eb; }
    .bg-danger-soft { background-color: #fef2f2; color: #dc2626; }
    .bg-secondary-soft { background-color: #f9fafb; color: #4b5563; }
    .table-hover tbody tr:hover { background-color: #f8fafc; }
    pre { white-space: pre-wrap; word-break: break-all; }
</style>
@endsection
