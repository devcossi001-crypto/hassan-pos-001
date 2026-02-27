@extends('layouts.app')

@section('title', 'System Status')
@section('page-title', 'System Management')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- System Status Card -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-gear"></i> System Status Management
                </h5>
            </div>
            <div class="card-body">
                <!-- System Status Alert -->
                <div class="alert {{ $systemStatus->is_active ? 'alert-success' : 'alert-danger' }} alert-lg mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="alert-heading mb-2">
                                <i class="bi {{ $systemStatus->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                System is {{ $systemStatus->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </h4>
                            @if (!$systemStatus->is_active && $systemStatus->status_reason)
                                <p class="mb-1">
                                    <strong>Reason:</strong> {{ $systemStatus->status_reason }}
                                </p>
                            @endif
                        </div>
                        <div class="btn-group" role="group">
                            @if ($systemStatus->is_active)
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                                    <i class="bi bi-power"></i> Deactivate
                                </button>
                            @else
                                <form action="{{ route('system.activate') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-power"></i> Activate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subscription Status Alert -->
                <div class="alert {{ $systemStatus->isSubscriptionValid() ? 'alert-info' : 'alert-warning' }} alert-lg mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="alert-heading mb-2">
                                <i class="bi bi-calendar-check"></i> Subscription Status
                            </h5>
                            @if ($systemStatus->subscription_end_date)
                                <p class="mb-1">
                                    <strong>End Date:</strong> {{ $systemStatus->subscription_end_date->format('M d, Y') }}
                                </p>
                                @if ($systemStatus->isSubscriptionValid())
                                    <p class="mb-0">
                                        <strong>Days Remaining:</strong> 
                                        <span class="badge bg-success">{{ $systemStatus->getDaysRemaining() }} days</span>
                                    </p>
                                @else
                                    <p class="mb-0 text-danger">
                                        <strong>⚠️ Subscription Expired</strong>
                                    </p>
                                @endif
                            @else
                                <p class="mb-0 text-warning">
                                    <strong>⚠️ No subscription set. Please set subscription end date below.</strong>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                    <div class="col-md-6">
                        <!-- Subscription Management -->
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Subscription Management</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('system.subscription.update') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="subscription_end_date" class="form-label">Subscription End Date</label>
                                        <input 
                                            type="date" 
                                            id="subscription_end_date" 
                                            name="subscription_end_date" 
                                            class="form-control @error('subscription_end_date') is-invalid @enderror"
                                            value="{{ $systemStatus->subscription_end_date ? $systemStatus->subscription_end_date->format('Y-m-d') : '' }}"
                                            required>
                                        <small class="text-muted d-block mt-2">
                                            Set the date when the subscription will expire. System will automatically deactivate after this date.
                                        </small>
                                        @error('subscription_end_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="bi bi-calendar-check"></i> Update Subscription
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">About System Status</h6>
                <ul class="list-unstyled text-sm">
                    <li class="mb-2">
                        <strong>Active System:</strong> All users can access and use the system normally (if subscription is valid).
                    </li>
                    <li class="mb-2">
                        <strong>Inactive System:</strong> Users attempting to access the system will be redirected and notified that the system is unavailable. Only owner can log in.
                    </li>
                    <li class="mb-2">
                        <strong>Subscription:</strong> System requires active subscription. If subscription expires, system automatically becomes inactive.
                    </li>
                    <li class="mb-2">
                        <strong>Owner Rights:</strong> Only the owner can change system status and manage subscriptions. Owner can access the system even when deactivated.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Deactivation Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deactivateModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Deactivate System
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('system.deactivate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>Warning:</strong> Deactivating the system will prevent all users (except owner) from accessing it.
                    </div>
                    <div class="mb-3">
                        <label for="status_reason" class="form-label">Reason for Deactivation *</label>
                        <textarea 
                            id="status_reason" 
                            name="status_reason" 
                            class="form-control @error('status_reason') is-invalid @enderror"
                            rows="3"
                            placeholder="e.g., System maintenance, database migration, security update..."
                            required>{{ old('status_reason') }}</textarea>
                        <small class="text-muted d-block mt-2">
                            This reason will be displayed to users when they try to access the system.
                        </small>
                        @error('status_reason')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-power"></i> Deactivate System
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
