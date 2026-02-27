@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h5>Product Inventory</h5>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
    </div>
</div>

<!-- Filter Tabs -->
<div class="mb-3">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $filter === 'active' ? 'active' : '' }}" 
               href="{{ route('products.index', ['filter' => 'active']) }}">
                <i class="bi bi-check-circle"></i> Active Products
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $filter === 'inactive' ? 'active' : '' }}" 
               href="{{ route('products.index', ['filter' => 'inactive']) }}">
                <i class="bi bi-dash-circle"></i> Inactive Products
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" 
               href="{{ route('products.index', ['filter' => 'all']) }}">
                <i class="bi bi-list"></i> All Products
            </a>
        </li>
    </ul>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>IMEI</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    @if(auth()->user()->isSuperAdmin())
                        <th>Cost</th>
                    @endif
                    <th>Selling Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td><strong>{{ $product->sku }}</strong></td>
                        <td><small>{{ $product->imei ?: '-' }}</small></td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>
                            @if ($product->quantity_in_stock <= $product->reorder_level)
                                <span class="badge bg-danger">{{ $product->quantity_in_stock }}</span>
                            @else
                                {{ $product->quantity_in_stock }}
                            @endif
                        </td>
                        @if(auth()->user()->isSuperAdmin())
                            <td>KES {{ number_format($product->cost_price, 2) }}</td>
                        @endif
                        <td><strong>KES {{ number_format($product->selling_price, 2) }}</strong></td>
                        <td>
                            @if ($product->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            @if ($filter === 'active')
                                No active products found
                            @elseif ($filter === 'inactive')
                                No inactive products found
                            @else
                                No products found
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="row mt-4">
    <div class="col">
        {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
