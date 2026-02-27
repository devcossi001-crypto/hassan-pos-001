@extends('layouts.app')

@section('title', 'Edit Trade-In')
@section('page-title', 'Edit Trade-In Product')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Edit Trade-In Information</h5>
                        <a href="{{ route('trade-ins.show', $tradeIn) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Details
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('trade-ins.update', $tradeIn) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Product Information -->
                        <h6 class="fw-bold mb-3 text-primary">Product Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name', $tradeIn->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $tradeIn->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">IMEI/Serial Number</label>
                                <input type="text" name="imei" class="form-control @error('imei') is-invalid @enderror" 
                                    value="{{ old('imei', $tradeIn->imei) }}">
                                @error('imei')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" 
                                    value="{{ old('sku', $tradeIn->sku) }}">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror" 
                                    value="{{ old('barcode', $tradeIn->barcode) }}">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $tradeIn->description) }}</textarea>
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
                                        value="{{ old('cost_price', $tradeIn->cost_price) }}" required>
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
                                        value="{{ old('selling_price', $tradeIn->selling_price) }}" required>
                                </div>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_in_stock" 
                                    class="form-control @error('quantity_in_stock') is-invalid @enderror" 
                                    value="{{ old('quantity_in_stock', $tradeIn->quantity_in_stock) }}" min="0" required>
                                @error('quantity_in_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Reorder Level</label>
                                <input type="number" name="reorder_level" 
                                    class="form-control @error('reorder_level') is-invalid @enderror" 
                                    value="{{ old('reorder_level', $tradeIn->reorder_level) }}" min="0">
                                @error('reorder_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-premium">
                                <i class="bi bi-check-circle me-2"></i>Update Trade-In
                            </button>
                            <a href="{{ route('trade-ins.show', $tradeIn) }}" class="btn btn-outline-secondary">
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
