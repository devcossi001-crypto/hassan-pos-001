@extends('layouts.app')

@section('title', 'Loan Details')
@section('page-title', 'Loan #' . $loan->id)

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Loan Info Card -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Loan Information</h5>
                <div>
                    @if($loan->status === 'active')
                        <span class="badge bg-warning">Active</span>
                    @elseif($loan->status === 'completed')
                        <span class="badge bg-success">Completed</span>
                    @else
                        <span class="badge bg-danger">Defaulted</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer:</strong> {{ $loan->customer_name }}<br>
                        <strong>Phone:</strong> {{ $loan->customer_phone }}<br>
                        @if($loan->customer_id_number)
                        <strong>ID:</strong> {{ $loan->customer_id_number }}<br>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Start Date:</strong> {{ $loan->start_date->format('d M Y') }}<br>
                        @if($loan->expected_end_date)
                        <strong>Expected End:</strong> {{ $loan->expected_end_date->format('d M Y') }}<br>
                        @endif
                        <strong>Duration:</strong> {{ $loan->duration_months ?? 'N/A' }} months<br>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <small class="text-muted">Total Amount</small>
                                <h5>KES {{ number_format($loan->total_amount, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success bg-opacity-10">
                            <div class="card-body">
                                <small class="text-muted">Paid</small>
                                <h5 class="text-success">KES {{ number_format($loan->paid_amount, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger bg-opacity-10">
                            <div class="card-body">
                                <small class="text-muted">Balance</small>
                                <h5 class="text-danger">KES {{ number_format($loan->balance, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info bg-opacity-10">
                            <div class="card-body">
                                <small class="text-muted">Progress</small>
                                <h5 class="text-info">{{ $loan->paymentProgress() }}%</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="progress mt-3" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $loan->paymentProgress() }}%"
                         aria-valuenow="{{ $loan->paymentProgress() }}" 
                         aria-valuemin="0" aria-valuemax="100">
                        {{ $loan->paymentProgress() }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment History</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Received By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loan->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td><strong>KES {{ number_format($payment->amount, 2) }}</strong></td>
                            <td><span class="badge bg-secondary">{{ $payment->payment_method }}</span></td>
                            <td>{{ $payment->reference ?? '-' }}</td>
                            <td>{{ $payment->receiver->name ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No payments recorded</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Add Payment Form -->
        @if($loan->status === 'active')
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Record Payment</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('loans.payment', $loan) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                        <small class="text-muted">Remaining: KES {{ number_format($loan->balance, 2) }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date *</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Method *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Record Payment</button>
                </form>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Actions</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('loans.index') }}" class="btn btn-secondary w-100 mb-2">
                    <i class="bi bi-arrow-left"></i> Back to Loans
                </a>
                @if($loan->status === 'active' && $loan->balance > 0)
                <form action="{{ route('loans.defaulted', $loan) }}" method="POST" class="d-inline w-100">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Mark this loan as defaulted?')">
                        <i class="bi bi-x-circle"></i> Mark as Defaulted
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
