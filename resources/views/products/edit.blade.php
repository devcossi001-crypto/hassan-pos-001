@extends('layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="row">
    <div class="col-md-11">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Product: {{ $product->name ?? 'N/A' }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category *</label>
                            <input type="text" name="category_id" class="form-control" value="{{ old('category_id', $product->category->name ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SKU (Auto-Generated)</label>
                            <input type="text" class="form-control bg-light" value="{{ $product->sku }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">IMEI Number</label>
                            <input type="text" name="imei" class="form-control" value="{{ old('imei', $product->imei) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Origin/Condition</label>
                            <select name="origin" class="form-control">
                                <option value="">-- Select Origin --</option>
                                <option value="New" {{ old('origin', $product->origin) == 'New' ? 'selected' : '' }}>New</option>
                                <option value="Ex UK" {{ old('origin', $product->origin) == 'Ex UK' ? 'selected' : '' }}>Ex UK</option>
                                <option value="Ex Japan" {{ old('origin', $product->origin) == 'Ex Japan' ? 'selected' : '' }}>Ex Japan</option>
                                <option value="Ex US" {{ old('origin', $product->origin) == 'Ex US' ? 'selected' : '' }}>Ex US</option>
                                <option value="Local Used" {{ old('origin', $product->origin) == 'Local Used' ? 'selected' : '' }}>Local Used</option>
                                <option value="Refurbished" {{ old('origin', $product->origin) == 'Refurbished' ? 'selected' : '' }}>Refurbished</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Reorder Level *</label>
                            <input type="number" name="reorder_level" class="form-control" value="{{ old('reorder_level', $product->reorder_level) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current Stock</label>
                            <input type="number" class="form-control bg-light" value="{{ $product->quantity_in_stock }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary bg-opacity-10">
                            <strong>Pricing Information</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Cost Price (KES)</label>
                                    <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control" 
                                           value="{{ old('cost_price', $product->cost_price) }}" 
                                           {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
                                    <small class="text-muted">
                                        @if(auth()->user()->isAdmin())
                                            Cost per item
                                        @else
                                            Not visible
                                        @endif
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Selling Price (KES) *</label>
                                    <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" 
                                           value="{{ old('selling_price', $product->selling_price) }}" required
                                           {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" id="quantity" class="form-control bg-light" 
                                           value="{{ $product->quantity_in_stock }}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Total Cost</label>
                                    <input type="text" id="display_cost" class="form-control bg-light" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-success">Total Selling</label>
                                    <input type="text" id="display_selling" class="form-control bg-light" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Product is Active
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Product</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = {
        cost: document.getElementById('cost_price'),
        selling: document.getElementById('selling_price'),
        qty: document.getElementById('quantity'),
        displayCost: document.getElementById('display_cost'),
        displaySelling: document.getElementById('display_selling')
    };

    // Calculate totals
    function calculate() {
        const cost = parseFloat(inputs.cost.value) || 0;
        const selling = parseFloat(inputs.selling.value) || 0;
        const qty = parseFloat(inputs.qty.value) || 0;
        
        inputs.displayCost.value = 'KES ' + (cost * qty).toFixed(2);
        inputs.displaySelling.value = 'KES ' + (selling * qty).toFixed(2);
    }

    // Event listeners
    inputs.cost.addEventListener('input', calculate);
    inputs.selling.addEventListener('input', calculate);

    // Initial calculation
    calculate();
});
</script>
@endsection
