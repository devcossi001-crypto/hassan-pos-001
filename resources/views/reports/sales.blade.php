@extends('layouts.app')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}" href="{{ route('reports.sales') }}">
                <i class="bi bi-receipt"></i> Sales Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.pnl') ? 'active' : '' }}" href="{{ route('reports.pnl') }}">
                <i class="bi bi-calculator"></i> Profit & Loss Statement
            </a>
        </li>
    </ul>

    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Report</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.sales') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>Total Sales</h6>
                    <div class="stat-value">KES {{ number_format($summary['total_sales'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>Transactions</h6>
                    <div class="stat-value">{{ $summary['total_transactions'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>Average Transaction</h6>
                    <div class="stat-value">KES {{ number_format($summary['average_transaction'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>M-Pesa Sales</h6>
                    <div class="stat-value">KES {{ number_format($summary['mpesa_sales'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->total_quantity }}</td>
                                <td>KES {{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
