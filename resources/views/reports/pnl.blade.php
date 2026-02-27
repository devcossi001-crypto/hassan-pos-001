@extends('layouts.app')

@section('title', 'Profit & Loss')
@section('page-title', 'Profit & Loss Statement')

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
            <form action="{{ route('reports.pnl') }}" method="GET" class="row g-3">
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

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Financial Summary</h6>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="fw-bold">Total Revenue (Sales)</td>
                                <td class="text-end">KES {{ number_format($revenue, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold ps-4 text-muted">Cost of Goods Sold (COGS)</td>
                                <td class="text-end text-danger">({{ number_format($cogs, 2) }})</td>
                            </tr>
                            <tr class="table-active">
                                <td class="fw-bold">Gross Profit</td>
                                <td class="text-end fw-bold">KES {{ number_format($grossProfit, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold ps-4 text-muted">Total Operating Expenses</td>
                                <td class="text-end text-danger">({{ number_format($expenses, 2) }})</td>
                            </tr>
                            <tr class="table-success">
                                <td class="fw-bold fs-5">Net Profit</td>
                                <td class="text-end fw-bold fs-5 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    KES {{ number_format($netProfit, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center py-2">
                                    <h6 class="text-muted small">Gross Margin</h6>
                                    <h5 class="mb-0">{{ $revenue > 0 ? number_format(($grossProfit / $revenue) * 100, 1) : 0 }}%</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center py-2">
                                    <h6 class="text-muted small">Net Margin</h6>
                                    <h5 class="mb-0">{{ $revenue > 0 ? number_format(($netProfit / $revenue) * 100, 1) : 0 }}%</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Expense Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenseBreakdown as $item)
                                    <tr>
                                        <td>{{ $item->category_name }}</td>
                                        <td class="text-end">KES {{ number_format($item->total_amount, 2) }}</td>
                                        <td class="text-end">
                                            {{ $expenses > 0 ? number_format(($item->total_amount / $expenses) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No expenses recorded for this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($expenses > 0)
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">KES {{ number_format($expenses, 2) }}</td>
                                    <td class="text-end">100%</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
