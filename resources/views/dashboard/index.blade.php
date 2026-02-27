@extends('layouts.app')

@section('title', 'Overview')
@section('page-title', 'Overview')

@section('content')
<!-- Stats Grid -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(45deg, #4f46e5, #0ea5e9);">
            <div class="card-body p-4 text-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="rounded-pill px-3 py-1 small" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">Today</div>
                    <button class="btn btn-sm text-white-50 p-0 border-0 toggle-card-visibility" data-target="sales">
                        <i class="bi bi-eye-slash-fill fs-5 eye-icon"></i>
                    </button>
                </div>
                <h6 class="text-white-50 mb-1 fw-medium">Daily Revenue</h6>
                <div class="h3 fw-bold mb-0">
                    <span class="masked-value" id="sales-masked">••••••</span>
                    <span class="actual-value" id="sales-actual" style="display: none;">KES {{ number_format($todaySales, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    @if(auth()->user()->isSuperAdmin())
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(45deg, #10b981, #059669);">
            <div class="card-body p-4 text-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="rounded-pill px-3 py-1 small" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">MTD</div>
                    <button class="btn btn-sm text-white-50 p-0 border-0 toggle-card-visibility" data-target="profit">
                        <i class="bi bi-eye-slash-fill fs-5 eye-icon"></i>
                    </button>
                </div>
                <h6 class="text-white-50 mb-1 fw-medium">Net Profit</h6>
                <div class="h3 fw-bold mb-0">
                    <span class="masked-value" id="profit-masked">••••••</span>
                    <span class="actual-value" id="profit-actual" style="display: none;">KES {{ number_format($mtdProfit, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(45deg, #6366f1, #4f46e5);">
            <div class="card-body p-4 text-white">
                <div class="d-flex justify-content-between align-items-center mb-3" style="min-height: 28px;">
                    <div class="rounded-pill px-3 py-1 small" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">MTD</div>
                    <button class="btn btn-sm text-white-50 p-0 border-0 toggle-card-visibility" data-target="mtd-revenue">
                        <i class="bi bi-eye-slash-fill fs-5 eye-icon"></i>
                    </button>
                </div>
                <h6 class="text-white-50 mb-1 fw-medium">MTD Revenue</h6>
                <div class="h3 fw-bold mb-0">
                    <span class="masked-value" id="mtd-revenue-masked">••••••</span>
                    <span class="actual-value" id="mtd-revenue-actual" style="display: none;">KES {{ number_format($mtdRevenue, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(45deg, #f59e0b, #d97706);">
            <div class="card-body p-4 text-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="badge rounded-pill bg-danger-soft text-white small" style="background: rgba(220, 38, 38, 0.4);">Alert</div>
                    <i class="bi bi-box-seam-fill fs-5 text-white-50"></i>
                </div>
                <h6 class="text-white-50 mb-1 fw-medium">Low Stock</h6>
                <div class="h3 fw-bold mb-0">{{ $lowStockProducts }} <span class="fs-6 fw-normal text-white-50">items</span></div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row g-4">
    <!-- Recent Sales Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Recent Activities</h5>
                <a href="{{ route('sales.index') }}" class="btn btn-sm px-3 text-primary fw-bold" style="background: #eef2ff; border-radius: 10px;">View Full History</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0 text-muted small text-uppercase">Receipt</th>
                                <th class="py-3 border-0 text-muted small text-uppercase">Customer</th>
                                <th class="py-3 border-0 text-muted small text-uppercase">Method</th>
                                <th class="py-3 border-0 text-muted small text-uppercase text-end px-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentSales as $sale)
                                <tr class="cursor-pointer" onclick="window.location='{{ route('sales.show', $sale) }}'">
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-primary">#{{ $sale->receipt_number }}</div>
                                        <div class="text-muted small">{{ $sale->created_at?->format('H:i A') ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $sale->customer?->name ?? 'Walk-in' }}</div>
                                        <div class="text-muted small">Standard Sale</div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 fw-medium {{ $sale->primary_payment_method == 'cash' ? 'bg-success-soft text-success' : 'bg-info-soft text-info' }}" 
                                              style="background: {{ $sale->primary_payment_method == 'cash' ? '#f0fdf4' : '#eff6ff' }};">
                                            {{ ucfirst($sale->primary_payment_method) }}
                                        </span>
                                    </td>
                                    <td class="text-end px-4 py-3 fw-bold text-dark fs-5">
                                        KES {{ number_format($sale->total_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <div class="mb-2"><i class="bi bi-inbox fs-1 opacity-25"></i></div>
                                        No sales recorded yet today
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Quick Operations</h5>
                
                @if ($activeShift)
                    <div class="alert alert-primary border-0 rounded-4 d-flex align-items-center gap-3 p-3 mb-4" style="background: #eef2ff;">
                        <i class="bi bi-info-circle-fill fs-4 text-primary"></i>
                        <div class="small">Shift active since {{ $activeShift->opened_at->format('H:i') }}</div>
                    </div>
                    
                    <a href="{{ route('sales.create') }}" class="btn btn-premium w-100 py-3 mb-3">
                        <i class="bi bi-cart-plus-fill me-2"></i> Launch POS System
                    </a>
                    
                    <button class="btn btn-light w-100 py-3 border-0 rounded-3 text-danger fw-bold" data-bs-toggle="modal" data-bs-target="#closeShiftModal">
                        <i class="bi bi-power me-2"></i> End Active Shift
                    </button>
                @else
                    <button class="btn btn-success w-100 py-4 rounded-4 mb-3 border-0 shadow-sm" style="background: #10b981;" data-bs-toggle="modal" data-bs-target="#openShiftModal">
                        <i class="bi bi-play-fill fs-4 me-2"></i> Start Work Shift
                    </button>
                    <p class="text-muted text-center small">You must open a shift before processing sales.</p>
                @endif

                <hr class="my-4 opacity-50">

                <h6 class="fw-bold mb-3">Inventory Health</h6>
                @if ($lowStockProducts > 0)
                    <div class="p-3 rounded-4 bg-warning-soft" style="background: #fffbeb;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                            <div>
                                <div class="fw-bold text-warning-emphasis">{{ $lowStockProducts }} Items Low</div>
                                <a href="{{ route('products.index') }}" class="small text-decoration-none">Review Stock &rarr;</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-3 rounded-4 bg-success-soft text-center" style="background: #f0fdf4;">
                        <i class="bi bi-check-circle-fill text-success mb-2 fs-2 d-block"></i>
                        <div class="small text-success fw-bold">All Stock Healthy</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Overhaul -->
<div class="modal fade" id="openShiftModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Shift Activation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('shifts.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Opening Floating Cash (KES)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" name="opening_cash" class="form-control border-start-0 py-2" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted text-uppercase fw-bold">Operational Notes</label>
                        <textarea name="opening_notes" class="form-control bg-light border-0" rows="3" placeholder="Brief details about current shift..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium rounded-3 px-4">Activate Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Close Shift Modal -->
@if($activeShift)
<div class="modal fade" id="closeShiftModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">End Work Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('shifts.close', $activeShift->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Closing Cash Counted (KES)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" name="closing_cash" class="form-control border-start-0 py-2" placeholder="0.00" required>
                        </div>
                        <small class="text-muted d-block mt-2">Enter the actual cash counted in the register</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted text-uppercase fw-bold">Closing Notes</label>
                        <textarea name="closing_notes" class="form-control bg-light border-0" rows="3" placeholder="Any discrepancies or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">End Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtns = document.querySelectorAll('.toggle-card-visibility');
    
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const maskedValue = document.getElementById(`${targetId}-masked`);
            const actualValue = document.getElementById(`${targetId}-actual`);
            const eyeIcon = this.querySelector('.eye-icon');
            
            const isHidden = actualValue.style.display === 'none';
            
            if (isHidden) {
                maskedValue.style.display = 'none';
                actualValue.style.display = 'inline';
                eyeIcon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            } else {
                maskedValue.style.display = 'inline';
                actualValue.style.display = 'none';
                eyeIcon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            }
        });
    });
});
</script>
@endpush
@endsection
