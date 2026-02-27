@extends('layouts.app')

@section('title', 'Record Loan')
@section('page-title', 'Record New Loan')

@section('content')
<div class="row">
    <div class="col-md-9 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Record Credit Sale / Loan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('loans.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Sale/Receipt *</label>
                            <select name="sale_id" class="form-control" required>
                                <option value="">-- Select Sale --</option>
                                @foreach($sales as $sale)
                                <option value="{{ $sale->id }}">
                                    Receipt #{{ $sale->receipt_number }} - KES {{ number_format($sale->total_amount, 2) }} ({{ $sale->created_at->format('d M Y') }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select the sale to convert to a loan</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Customer Information</h6>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Customer Name *</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone Number *</label>
                            <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ID Number</label>
                            <input type="text" name="customer_id_number" class="form-control" value="{{ old('customer_id_number') }}">
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Loan Details</h6>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Total Amount *</label>
                            <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" value="{{ old('total_amount') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Down Payment</label>
                            <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control" value="{{ old('paid_amount', 0) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Monthly Payment</label>
                            <input type="number" step="0.01" name="monthly_payment" class="form-control" value="{{ old('monthly_payment') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Duration (Months)</label>
                            <input type="number" name="duration_months" class="form-control" value="{{ old('duration_months') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Balance</label>
                            <input type="text" id="balance" class="form-control bg-light" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Record Loan</button>
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalInput = document.getElementById('total_amount');
    const paidInput = document.getElementById('paid_amount');
    const balanceInput = document.getElementById('balance');

    function calculateBalance() {
        const total = parseFloat(totalInput.value) || 0;
        const paid = parseFloat(paidInput.value) || 0;
        const balance = total - paid;
        balanceInput.value = 'KES ' + balance.toFixed(2);
    }

    totalInput.addEventListener('input', calculateBalance);
    paidInput.addEventListener('input', calculateBalance);

    calculateBalance();
});
</script>
@endsection
