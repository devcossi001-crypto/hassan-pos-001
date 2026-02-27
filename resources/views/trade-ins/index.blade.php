@extends('layouts.app')

@section('title', 'Trade-Ins')
@section('page-title', 'Trade-In Management')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Trade-Ins</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_trade_ins'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-arrow-left-right text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Value</p>
                            <h3 class="mb-0 fw-bold">KES {{ number_format($stats['total_value'], 2) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-cash-stack text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">In Stock</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['in_stock'] }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-box-seam text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Sold Out</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['sold_out'] }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trade-Ins Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Trade-In Products</h5>
                <a href="{{ route('trade-ins.create') }}" class="btn btn-premium">
                    <i class="bi bi-plus-circle me-2"></i>Record Trade-In
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($tradeIns->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">IMEI/Serial</th>
                            <th class="py-3">Trade-In Value</th>
                            <th class="py-3">Selling Price</th>
                            <th class="py-3">Stock</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tradeIns as $tradeIn)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-semibold">{{ $tradeIn->name }}</div>
                                @if($tradeIn->sku)
                                <small class="text-muted">SKU: {{ $tradeIn->sku }}</small>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge badge-premium bg-primary bg-opacity-10 text-primary">
                                    {{ $tradeIn->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <small class="text-muted">{{ $tradeIn->imei ?? 'N/A' }}</small>
                            </td>
                            <td class="py-3">
                                <span class="fw-semibold">KES {{ number_format($tradeIn->cost_price, 2) }}</span>
                            </td>
                            <td class="py-3">
                                <span class="fw-semibold text-success">KES {{ number_format($tradeIn->selling_price, 2) }}</span>
                            </td>
                            <td class="py-3">
                                @if($tradeIn->quantity_in_stock > 0)
                                <span class="badge bg-success">{{ $tradeIn->quantity_in_stock }}</span>
                                @else
                                <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($tradeIn->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('trade-ins.show', $tradeIn) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('trade-ins.edit', $tradeIn) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top">
                {{ $tradeIns->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">No trade-in products recorded yet.</p>
                <a href="{{ route('trade-ins.create') }}" class="btn btn-premium mt-2">
                    <i class="bi bi-plus-circle me-2"></i>Record Your First Trade-In
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
