@extends('layouts.app')

@section('title', 'Create Invoice')
@section('page-title', 'Create New Invoice')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Invoice Details</h5>
                        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('invoices.store') }}" method="POST">
                        @csrf

                        <h6 class="fw-bold mb-3 text-primary">Customer Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->phone ?? 'No phone' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary">Invoice Dates</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                    value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                                    value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary">Invoice Amounts</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Subtotal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="number" step="0.01" name="subtotal" 
                                        class="form-control @error('subtotal') is-invalid @enderror" 
                                        value="{{ old('subtotal') }}" required id="subtotal">
                                </div>
                                @error('subtotal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tax Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="number" step="0.01" name="tax_amount" 
                                        class="form-control @error('tax_amount') is-invalid @enderror" 
                                        value="{{ old('tax_amount', 0) }}" id="tax_amount">
                                </div>
                                @error('tax_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Discount Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="number" step="0.01" name="discount_amount" 
                                        class="form-control @error('discount_amount') is-invalid @enderror" 
                                        value="{{ old('discount_amount', 0) }}" id="discount_amount">
                                </div>
                                @error('discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="number" step="0.01" name="total_amount" 
                                        class="form-control @error('total_amount') is-invalid @enderror" 
                                        value="{{ old('total_amount') }}" required id="total_amount" readonly>
                                </div>
                                @error('total_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary">Additional Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Terms & Conditions</label>
                                <textarea name="terms" rows="2" class="form-control @error('terms') is-invalid @enderror">{{ old('terms') }}</textarea>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Invoice
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const total = subtotal + tax - discount;
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    document.getElementById('subtotal').addEventListener('input', calculateTotal);
    document.getElementById('tax_amount').addEventListener('input', calculateTotal);
    document.getElementById('discount_amount').addEventListener('input', calculateTotal);
</script>
@endsection
