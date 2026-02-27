@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">Invoices</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create Invoice
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Total Invoices</p>
                            <h4 class="mb-0">{{ $stats['total_invoices'] }}</h4>
                        </div>
                        <i class="bi bi-receipt text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Pending</p>
                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                        </div>
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Partially Paid</p>
                            <h4 class="mb-0">{{ $stats['partially_paid'] }}</h4>
                        </div>
                        <i class="bi bi-cash-coin text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Overdue</p>
                            <h4 class="mb-0 text-danger">{{ $stats['overdue'] }}</h4>
                        </div>
                        <i class="bi bi-exclamation-circle text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Total</th>
                            <th>Amount Paid</th>
                            <th>Amount Due</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>{{ $invoice->customer->name }}</td>
                                <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                <td>KES {{ number_format($invoice->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-success">KES {{ number_format($invoice->amount_paid, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">KES {{ number_format($invoice->amount_due, 2) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'sent' => 'info',
                                            'pending' => 'warning',
                                            'partially_paid' => 'primary',
                                            'paid' => 'success',
                                            'overdue' => 'danger',
                                            'cancelled' => 'dark'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($invoice->status === 'draft')
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No invoices found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
