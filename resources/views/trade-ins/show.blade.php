@extends('layouts.app')

@section('title', 'Trade-In Details')
@section('page-title', 'Trade-In Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Product Information</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('trade-ins.edit', $tradeIn) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                            <a href="{{ route('trade-ins.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Product Name</label>
                            <p class="fw-semibold mb-0">{{ $tradeIn->name }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">Category</label>
                            <p class="mb-0">
                                <span class="badge badge-premium bg-primary bg-opacity-10 text-primary">
                                    {{ $tradeIn->category->name ?? 'N/A' }}
                                </span>
                            </p>
                        </div>

                        <div class="col-md-4">
                            <label class="text-muted small">SKU</label>
                            <p class="fw-semibold mb-0">{{ $tradeIn->sku ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-4">
                            <label class="text-muted small">IMEI/Serial</label>
                            <p class="fw-semibold mb-0">{{ $tradeIn->imei ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-4">
                            <label class="text-muted small">Barcode</label>
                            <p class="fw-semibold mb-0">{{ $tradeIn->barcode ?? 'N/A' }}</p>
                        </div>

                        @if($tradeIn->description)
                        <div class="col-12">
                            <label class="text-muted small">Description</label>
                            <p class="mb-0" style="white-space: pre-line;">{{ $tradeIn->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sales History -->
            @if($tradeIn->saleItems->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Sales History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="py-3">Sale ID</th>
                                    <th class="py-3">Quantity</th>
                                    <th class="py-3">Price</th>
                                    <th class="py-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tradeIn->saleItems as $item)
                                <tr>
                                    <td class="px-4 py-3">{{ $item->sale->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-3">
                                        <a href="{{ route('sales.show', $item->sale) }}" class="text-decoration-none">
                                            #{{ $item->sale->id }}
                                        </a>
                                    </td>
                                    <td class="py-3">{{ $item->quantity }}</td>
                                    <td class="py-3">KES {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-3 fw-semibold">KES {{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Pricing Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Pricing & Stock</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small">Trade-In Value (Cost)</label>
                        <h4 class="mb-0 fw-bold text-danger">KES {{ number_format($tradeIn->cost_price, 2) }}</h4>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Selling Price</label>
                        <h4 class="mb-0 fw-bold text-success">KES {{ number_format($tradeIn->selling_price, 2) }}</h4>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Potential Profit</label>
                        <h4 class="mb-0 fw-bold text-primary">KES {{ number_format($tradeIn->profit, 2) }}</h4>
                        <small class="text-muted">Margin: {{ $tradeIn->profit_margin }}%</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="text-muted small">Current Stock</label>
                        <h4 class="mb-0 fw-bold">{{ $tradeIn->quantity_in_stock }}</h4>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Total Value</label>
                        <h5 class="mb-0 fw-bold">KES {{ number_format($tradeIn->total_cost, 2) }}</h5>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small">Status</label>
                        <div>
                            @if($tradeIn->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif

                            @if($tradeIn->quantity_in_stock > 0)
                            <span class="badge bg-info">In Stock</span>
                            @else
                            <span class="badge bg-danger">Out of Stock</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metadata Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Metadata</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small">Origin</label>
                        <p class="mb-0">
                            <span class="badge bg-warning text-dark">{{ $tradeIn->origin }}</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Recorded On</label>
                        <p class="mb-0">{{ $tradeIn->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small">Last Updated</label>
                        <p class="mb-0">{{ $tradeIn->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
