@extends('layouts.app')

@section('title', 'Loans')
@section('page-title', 'Loan Management')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <h6 class="text-muted">Total Loans</h6>
                        <h3 class="mb-0">{{ $stats['total_loans'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="text-muted">Active Loans</h6>
                        <h3 class="mb-0 text-warning">{{ $stats['active_loans'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="text-muted">Overdue</h6>
                        <h3 class="mb-0 text-danger">{{ $stats['overdue_loans'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <h6 class="text-muted">Outstanding</h6>
                        <h3 class="mb-0 text-success">KES {{ number_format($stats['total_outstanding'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loans Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Loans</h5>
                <a href="{{ route('loans.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Record New Loan
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Loan #</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            <tr>
                                <td><strong>#{{ $loan->id }}</strong></td>
                                <td>{{ $loan->customer_name }}</td>
                                <td>{{ $loan->customer_phone }}</td>
                                <td>KES {{ number_format($loan->total_amount, 2) }}</td>
                                <td>KES {{ number_format($loan->paid_amount, 2) }}</td>
                                <td><strong>KES {{ number_format($loan->balance, 2) }}</strong></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $loan->paymentProgress() }}%"
                                             aria-valuenow="{{ $loan->paymentProgress() }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ $loan->paymentProgress() }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($loan->status === 'active')
                                        <span class="badge bg-warning">Active</span>
                                    @elseif($loan->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Defaulted</span>
                                    @endif
                                </td>
                                <td>{{ $loan->start_date->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No loans recorded</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
