@extends('layouts.app')

@section('title', 'Record Trade-In')
@section('page-title', 'Record New Trade-In')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Trade-In Information</h5>
                        <a href="{{ route('trade-ins.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('trade-ins.store') }}" method="POST">
                        @csrf

                        <!-- Product Information -->
                        <h6 class="fw-bold mb-3 text-primary">Product Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" 
                                    value="{{ old('category') }}" required>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">IMEI/Serial Number</label>
                                <input type="text" name="imei" class="form-control @error('imei') is-invalid @enderror" 
                                    value="{{ old('imei') }}">
                                @error('imei')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" 
                                    value="{{ old('sku') }}">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror" 
                                    value="{{ old('barcode') }}">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Pricing Information -->
                        <h6 class="fw-bold mb-3 text-primary">Pricing & Stock</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Trade-In Value (Cost) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="number" step="0.01" name="cost_price" 
                                        class="form-control @error('cost_price') is-invalid @enderror" 
                                        value="{{ old('cost_price') }}" required>
                                </div>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="number" step="0.01" name="selling_price" 
                                        class="form-control @error('selling_price') is-invalid @enderror" 
                                        value="{{ old('selling_price') }}" required>
                                </div>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_in_stock" 
                                    class="form-control @error('quantity_in_stock') is-invalid @enderror" 
                                    value="{{ old('quantity_in_stock', 1) }}" min="1" required>
                                @error('quantity_in_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Reorder Level</label>
                                <input type="number" name="reorder_level" 
                                    class="form-control @error('reorder_level') is-invalid @enderror" 
                                    value="{{ old('reorder_level', 0) }}" min="0">
                                @error('reorder_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Customer Information (Optional) -->
                        <h6 class="fw-bold mb-3 text-primary">Customer Information (Optional)</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" 
                                    class="form-control @error('customer_name') is-invalid @enderror" 
                                    value="{{ old('customer_name') }}">
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Customer Phone</label>
                                <input type="text" name="customer_phone" 
                                    class="form-control @error('customer_phone') is-invalid @enderror" 
                                    value="{{ old('customer_phone') }}">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Additional Notes</label>
                                <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-premium">
                                <i class="bi bi-check-circle me-2"></i>Record Trade-In
                            </button>
                            <a href="{{ route('trade-ins.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
