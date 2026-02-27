@extends('layouts.app')

@section('title', 'Create Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="row">
    <div class="col-md-11">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add New Product</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category *</label>
                            <input type="text" name="category_id" class="form-control" value="{{ old('category_id') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SKU (Auto-Generated)</label>
                            <input type="text" class="form-control bg-light" value="Generated on save" readonly>
                            <small class="text-muted">SKU will be automatically generated when you save</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Origin/Condition</label>
                            <select name="origin" class="form-control">
                                <option value="">-- Select Origin --</option>
                                <option value="New">New</option>
                                <option value="Ex UK">Ex UK</option>
                                <option value="Ex Japan">Ex Japan</option>
                                <option value="Ex US">Ex US</option>
                                <option value="Local Used">Local Used</option>
                                <option value="Refurbished">Refurbished</option>
                            </select>
                            <small class="text-muted">Indicates product source/condition</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reorder Level *</label>
                            <input type="number" name="reorder_level" class="form-control" value="{{ old('reorder_level', 10) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="1">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Entry Mode Selection -->
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary bg-opacity-10">
                            <strong>Entry Mode</strong>
                        </div>
                        <div class="card-body">
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="imei_mode" id="mode_single" value="single" checked>
                                <label class="btn btn-outline-primary" for="mode_single">Single Item</label>

                                <input type="radio" class="btn-check" name="imei_mode" id="mode_bulk" value="bulk">
                                <label class="btn btn-outline-primary" for="mode_bulk">Bulk (Same Price)</label>

                                <input type="radio" class="btn-check" name="imei_mode" id="mode_individual" value="individual">
                                <label class="btn btn-outline-success" for="mode_individual">Individual Pricing</label>
                            </div>

                            <!-- Single Mode -->
                            <div id="single_container">
                                <input type="text" name="imei" class="form-control" placeholder="IMEI (optional)">
                            </div>

                            <!-- Bulk Mode -->
                            <div id="bulk_container" class="d-none">
                                <textarea name="imei_list" class="form-control" rows="5" placeholder="One IMEI per line"></textarea>
                                <small class="text-muted">Count: <span id="imei_count">0</span></small>
                            </div>

                            <!-- Individual Mode -->
                            <div id="individual_container" class="d-none">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Items with Individual Pricing</strong>
                                    <button type="button" class="btn btn-sm btn-success" id="add_row">+ Add Row</button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="25%">IMEI</th>
                                                <th width="18%">Cost Price</th>
                                                <th width="18%">Selling Price</th>
                                                <th width="12%">Qty</th>
                                                <th width="15%">Total Cost</th>
                                                <th width="15%">Total Selling</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="imei_tbody"></tbody>
                                        <tfoot class="table-secondary fw-bold">
                                            <tr>
                                                <td>TOTAL</td>
                                                <td><small>Avg: </small><span id="avg_cost">0</span></td>
                                                <td><small>Avg: </small><span id="avg_selling">0</span></td>
                                                <td id="total_qty">0</td>
                                                <td id="total_cost">KES 0</td>
                                                <td id="total_selling">KES 0</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="copy_all">Copy First to All</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="clear_all">Clear All</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Standard Pricing (Single & Bulk) -->
                    <div id="standard_pricing">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Cost Price (KES)</label>
                                <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Selling Price (KES) *</label>
                                <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity_in_stock" id="quantity" class="form-control" value="0">
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

                    <!-- Summary (Individual Mode) -->
                    <div id="summary" class="d-none">
                        <div class="alert alert-success">
                            <div class="row text-center">
                                <div class="col-3">
                                    <small>Total Items</small>
                                    <h5 id="sum_qty">0</h5>
                                </div>
                                <div class="col-3">
                                    <small>Total Cost</small>
                                    <h5 id="sum_cost">KES 0</h5>
                                </div>
                                <div class="col-3">
                                    <small>Total Selling</small>
                                    <h5 id="sum_selling">KES 0</h5>
                                </div>
                                <div class="col-3">
                                    <small>Profit</small>
                                    <h5 id="sum_profit" class="text-success">KES 0</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Create Product</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modes = {
        single: document.getElementById('mode_single'),
        bulk: document.getElementById('mode_bulk'),
        individual: document.getElementById('mode_individual')
    };
    
    const containers = {
        single: document.getElementById('single_container'),
        bulk: document.getElementById('bulk_container'),
        individual: document.getElementById('individual_container'),
        pricing: document.getElementById('standard_pricing'),
        summary: document.getElementById('summary')
    };
    
    const inputs = {
        imeiList: document.querySelector('[name="imei_list"]'),
        cost: document.getElementById('cost_price'),
        selling: document.getElementById('selling_price'),
        qty: document.getElementById('quantity'),
        displayCost: document.getElementById('display_cost'),
        displaySelling: document.getElementById('display_selling')
    };
    
    const tbody = document.getElementById('imei_tbody');
    let rowId = 0;

    // Mode switching
    function switchMode() {
        const mode = document.querySelector('[name="imei_mode"]:checked').value;
        
        containers.single.classList.add('d-none');
        containers.bulk.classList.add('d-none');
        containers.individual.classList.add('d-none');
        containers.pricing.classList.remove('d-none');
        containers.summary.classList.add('d-none');
        
        if (mode === 'single') {
            containers.single.classList.remove('d-none');
            inputs.qty.readOnly = false;
            inputs.cost.readOnly = false;
            inputs.selling.readOnly = false;
            inputs.selling.required = true;
        } else if (mode === 'bulk') {
            containers.bulk.classList.remove('d-none');
            inputs.qty.readOnly = true;
            inputs.selling.required = true;
            updateBulkCount();
        } else {
            containers.individual.classList.remove('d-none');
            containers.pricing.classList.add('d-none');
            containers.summary.classList.remove('d-none');
            inputs.selling.required = false; // Disable validation for hidden field
            if (tbody.children.length === 0) {
                addRow(); addRow(); addRow();
            }
            updateTotals();
        }
        calculate();
    }

    // Bulk count
    function updateBulkCount() {
        const lines = (inputs.imeiList.value || '').split('\n').filter(l => l.trim());
        document.getElementById('imei_count').textContent = lines.length;
        inputs.qty.value = lines.length;
        calculate();
    }

    // Standard calculation
    function calculate() {
        const cost = parseFloat(inputs.cost.value) || 0;
        const selling = parseFloat(inputs.selling.value) || 0;
        const qty = parseFloat(inputs.qty.value) || 0;
        
        inputs.displayCost.value = 'KES ' + (cost * qty).toFixed(2);
        inputs.displaySelling.value = 'KES ' + (selling * qty).toFixed(2);
    }

    // Add row
    function addRow() {
        rowId++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="imei_entries[${rowId}][imei]" class="form-control form-control-sm" placeholder="IMEI"></td>
            <td><input type="number" name="imei_entries[${rowId}][cost_price]" class="form-control form-control-sm row-cost" step="0.01" value="0"></td>
            <td><input type="number" name="imei_entries[${rowId}][selling_price]" class="form-control form-control-sm row-selling" step="0.01" required></td>
            <td><input type="number" name="imei_entries[${rowId}][quantity]" class="form-control form-control-sm row-qty" value="1" min="1"></td>
            <td><input type="text" class="form-control form-control-sm bg-light row-total-cost" readonly value="KES 0"></td>
            <td><input type="text" class="form-control form-control-sm bg-light row-total-selling" readonly value="KES 0"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">×</button></td>
        `;
        
        tbody.appendChild(tr);
        
        tr.querySelectorAll('input[type="number"]').forEach(inp => {
            inp.addEventListener('input', () => updateRow(tr));
        });
        
        tr.querySelector('.remove-row').addEventListener('click', () => {
            if (tbody.children.length > 1) {
                tr.remove();
                updateTotals();
            } else {
                alert('Need at least one row');
            }
        });
        
        updateRow(tr);
    }

    // Update row
    function updateRow(tr) {
        const cost = parseFloat(tr.querySelector('.row-cost').value) || 0;
        const selling = parseFloat(tr.querySelector('.row-selling').value) || 0;
        const qty = parseFloat(tr.querySelector('.row-qty').value) || 0;
        
        tr.querySelector('.row-total-cost').value = 'KES ' + (cost * qty).toFixed(2);
        tr.querySelector('.row-total-selling').value = 'KES ' + (selling * qty).toFixed(2);
        
        updateTotals();
    }

    // Update totals
    function updateTotals() {
        const rows = tbody.querySelectorAll('tr');
        let totalQty = 0, totalCost = 0, totalSelling = 0, sumCost = 0, sumSelling = 0, count = 0;
        
        rows.forEach(tr => {
            const cost = parseFloat(tr.querySelector('.row-cost').value) || 0;
            const selling = parseFloat(tr.querySelector('.row-selling').value) || 0;
            const qty = parseFloat(tr.querySelector('.row-qty').value) || 0;
            
            totalQty += qty;
            totalCost += cost * qty;
            totalSelling += selling * qty;
            sumCost += cost;
            sumSelling += selling;
            count++;
        });
        
        const avgCost = count > 0 ? (sumCost / count).toFixed(2) : 0;
        const avgSelling = count > 0 ? (sumSelling / count).toFixed(2) : 0;
        const profit = totalSelling - totalCost;
        
        document.getElementById('total_qty').textContent = totalQty;
        document.getElementById('avg_cost').textContent = avgCost;
        document.getElementById('avg_selling').textContent = avgSelling;
        document.getElementById('total_cost').textContent = 'KES ' + totalCost.toFixed(2);
        document.getElementById('total_selling').textContent = 'KES ' + totalSelling.toFixed(2);
        
        document.getElementById('sum_qty').textContent = totalQty;
        document.getElementById('sum_cost').textContent = 'KES ' + totalCost.toFixed(2);
        document.getElementById('sum_selling').textContent = 'KES ' + totalSelling.toFixed(2);
        document.getElementById('sum_profit').textContent = 'KES ' + profit.toFixed(2);
        document.getElementById('sum_profit').className = profit >= 0 ? 'text-success' : 'text-danger';
        
        inputs.qty.value = totalQty;
    }

    // Copy first to all
    document.getElementById('copy_all').addEventListener('click', () => {
        const rows = tbody.querySelectorAll('tr');
        if (rows.length === 0) return;
        
        const first = rows[0];
        const cost = first.querySelector('.row-cost').value;
        const selling = first.querySelector('.row-selling').value;
        
        if (confirm(`Copy first row prices?\nCost: ${cost}\nSelling: ${selling}`)) {
            rows.forEach((tr, i) => {
                if (i > 0) {
                    tr.querySelector('.row-cost').value = cost;
                    tr.querySelector('.row-selling').value = selling;
                    updateRow(tr);
                }
            });
        }
    });

    // Clear all
    document.getElementById('clear_all').addEventListener('click', () => {
        if (confirm('Clear all rows?')) {
            tbody.innerHTML = '';
            rowId = 0;
            addRow(); addRow(); addRow();
        }
    });

    // Event listeners
    Object.values(modes).forEach(m => m.addEventListener('change', switchMode));
    inputs.imeiList.addEventListener('input', updateBulkCount);
    inputs.cost.addEventListener('input', calculate);
    inputs.selling.addEventListener('input', calculate);
    inputs.qty.addEventListener('input', calculate);
    document.getElementById('add_row').addEventListener('click', addRow);

    // Form validation
    document.querySelector('form').addEventListener('submit', (e) => {
        const mode = document.querySelector('[name="imei_mode"]:checked').value;
        
        if (mode === 'individual') {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length === 0) {
                e.preventDefault();
                alert('Add at least one row');
                return;
            }
            
            // Check duplicates
            const imeis = [];
            let hasDupe = false;
            rows.forEach(tr => {
                const imei = tr.querySelector('[name^="imei_entries"]').value.trim();
                if (imei && imeis.includes(imei)) hasDupe = true;
                if (imei) imeis.push(imei);
            });
            
            if (hasDupe) {
                e.preventDefault();
                alert('Duplicate IMEIs found');
            }
        }
    });

    switchMode();
});
</script>
@endsection