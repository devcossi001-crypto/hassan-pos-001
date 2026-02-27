@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)
@section('page-title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">{{ $invoice->invoice_number }}</h5>
                        <div>
                            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Invoice Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Invoice Information</h6>
                            <p class="mb-1"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                            <p class="mb-1"><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}</p>
                            <p class="mb-1"><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Bill To</h6>
                            <p class="mb-1"><strong>{{ $invoice->customer->name }}</strong></p>
                            <p class="mb-1">{{ $invoice->customer->phone }}</p>
                            <p class="mb-1">{{ $invoice->customer->email }}</p>
                            <p class="mb-0">{{ $invoice->customer->address }}</p>
                        </div>
                    </div>

                    <!-- Status and Amount Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="text-muted mb-1 small">Status</p>
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
                                    <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }} fs-6">
                                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="text-muted mb-1 small">Total Amount</p>
                                    <h5 class="mb-0">KES {{ number_format($invoice->total_amount, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="text-muted mb-1 small">Amount Paid</p>
                                    <h5 class="mb-0 text-success">KES {{ number_format($invoice->amount_paid, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="text-muted mb-1 small">Amount Due</p>
                                    <h5 class="mb-0 text-warning">KES {{ number_format($invoice->amount_due, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end"><strong>KES {{ number_format($invoice->subtotal, 2) }}</strong></td>
                                </tr>
                                @if($invoice->tax_amount > 0)
                                    <tr>
                                        <td>Tax:</td>
                                        <td class="text-end"><strong>KES {{ number_format($invoice->tax_amount, 2) }}</strong></td>
                                    </tr>
                                @endif
                                @if($invoice->discount_amount > 0)
                                    <tr>
                                        <td>Discount:</td>
                                        <td class="text-end"><strong>-KES {{ number_format($invoice->discount_amount, 2) }}</strong></td>
                                    </tr>
                                @endif
                                <tr class="border-top">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong>KES {{ number_format($invoice->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Paid:</strong></td>
                                    <td class="text-end"><strong class="text-success">KES {{ number_format($invoice->amount_paid, 2) }}</strong></td>
                                </tr>
                                <tr class="bg-light">
                                    <td><strong>Balance:</strong></td>
                                    <td class="text-end"><strong class="text-warning">KES {{ number_format($invoice->amount_due, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Payments Section -->
                    @if($invoice->payments->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Payment History</h6>
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th>Received By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                <td><strong>KES {{ number_format($payment->amount, 2) }}</strong></td>
                                                <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                                <td>{{ $payment->receivedBy->name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($invoice->notes)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-2">Notes</h6>
                                <p class="text-muted">{{ $invoice->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                @if($invoice->status === 'draft')
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a>
                                    <form action="{{ route('invoices.send', $invoice) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-info">
                                            <i class="bi bi-send me-2"></i>Send to Customer
                                        </button>
                                    </form>
                                @endif

                                @if($invoice->amount_due > 0 && $invoice->status !== 'cancelled')
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                        <i class="bi bi-cash-coin me-2"></i>Record Payment
                                    </button>
                                @endif

                                @if($invoice->status === 'draft' && $invoice->amount_paid === 0)
                                    <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash me-2"></i>Cancel Invoice
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('invoices.payment', $invoice) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" step="0.01" name="amount" class="form-control" 
                                value="{{ old('amount') }}" max="{{ $invoice->amount_due }}" required>
                        </div>
                        <small class="text-muted">Maximum: KES {{ number_format($invoice->amount_due, 2) }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control" required>
                            <option value="">-- Select Method --</option>
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-PESA</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="e.g., Cheque #, Receipt #">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
