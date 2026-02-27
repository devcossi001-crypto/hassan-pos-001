@extends('layouts.app')

@section('title', 'Anisa Hub')

@section('page-title', 'Point of Sale (POS)')

@section('content')
<style>
    body {
        background: url('/images/pos-bg.png') no-repeat center center fixed;
        background-size: cover;
        position: relative;
    }
    
    body::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(244, 247, 254, 0.4);
        backdrop-filter: blur(2px);
        z-index: -1;
    }

    .card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border-radius: 12px;
    }

    .card-header {
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>
<div class="container-fluid">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row">
        <!-- Left Panel - Product Search & Cart -->
        <div class="col-md-8">
            <!-- Product Search -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Search Products</h5>
                </div>
                <div class="card-body">
                    <input type="text" id="productSearch" class="form-control" placeholder="Search by product name, code, or barcode...">
                    <div id="searchResults" class="mt-2">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sale Items Cart -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sale Items</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-danger" id="clearCart">Clear</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Description</th>
                                    <th width="100">Qty</th>
                                    <th width="120">Unit Price</th>
                                    <th width="120">Total</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                            </tbody>
                            <tfoot>
                                <tr id="emptyCart">
                                    <td colspan="5" class="text-center text-muted">No items added yet</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Checkout -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Checkout</h5>
                </div>
                <div class="card-body">
                    <form id="posForm" method="POST" action="{{ route('sales.store') }}">
                        @csrf

                        <!-- Totals Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <strong id="subtotal">KES 0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Discount</span>
                                <input type="number" name="discount" id="discount" class="form-control form-control-sm text-end" value="0" min="0" step="0.01" style="width: 100px;">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Total Amount</h5>
                                <h5 class="text-primary" id="totalAmount">KES 0.00</h5>
                            </div>
                        </div>

                        <!-- Customer Selection -->
                        <div class="mb-3">
                            <label class="form-label">👤 Customer</label>
                            <select name="customer_id" id="customer" class="form-select">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" id="paymentMethod" class="form-select" required>
                                <option value="cash">💵 Cash</option>
                                <option value="mpesa">📱 M-Pesa</option>
                                <option value="card">💳 Card</option>
                                <option value="credit">📋 Credit</option>
                            </select>
                        </div>

                        <!-- M-Pesa Phone Number (Hidden by default) -->
                        <div class="mb-3" id="mpesaPhoneField" style="display: none;">
                            <label class="form-label">M-Pesa Phone Number</label>
                            <input type="tel" name="mpesa_phone" id="mpesaPhone" class="form-control" placeholder="07XXXXXXXX or 2547XXXXXXXX">
                            <small class="text-muted">Enter the phone number to receive STK Push</small>
                        </div>

                        <!-- Amount Tendered -->
                        <div class="mb-3" id="amountTenderedField">
                            <label class="form-label">Amount Tendered</label>
                            <input type="number" name="amount_tendered" id="amountTendered" class="form-control" step="0.01" min="0">
                        </div>

                        <!-- Change Due -->
                        <div class="mb-4" id="changeDueField">
                            <div class="d-flex justify-content-between">
                                <strong>Change Due</strong>
                                <strong class="text-success" id="changeDue">KES 0.00</strong>
                            </div>
                        </div>

                        <!-- Hidden Cart Data -->
                        <input type="hidden" name="cart_data" id="cartData">
                        <input type="hidden" name="subtotal_amount" id="subtotalInput">
                        <input type="hidden" name="tax_amount" id="taxInput">
                        <input type="hidden" name="total_amount" id="totalInput">

                        <!-- Action Buttons -->
                        @if(!($hasActiveShift ?? true))
                            <div class="alert alert-warning">
                                ⚠️ Open a shift to complete sales
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="completeSaleBtn" @if(!($hasActiveShift ?? true)) disabled @endif>
                                ✓ Complete Sale
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="cancelBtn">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- M-Pesa Payment Modal -->
<div class="modal fade" id="mpesaModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">📱 M-Pesa Payment</h5>
            </div>
            <div class="modal-body text-center">
                <div id="mpesaStatusPending" style="display: none;">
                    <div class="spinner-border text-success mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mb-3">Waiting for Payment...</h5>
                    <p class="text-muted">
                        1. Check your phone for M-Pesa prompt<br>
                        2. Enter your M-Pesa PIN<br>
                        3. Confirm the payment
                    </p>
                    <p class="fw-bold" id="mpesaPhoneDisplay"></p>
                    <p class="fw-bold text-success" id="mpesaAmountDisplay"></p>
                </div>
                <div id="mpesaStatusSuccess" style="display: none;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-success">Payment Successful!</h5>
                    <p id="mpesaReceiptNumber"></p>
                </div>
                <div id="mpesaStatusFailed" style="display: none;">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-danger">Payment Failed</h5>
                    <p id="mpesaErrorMessage" class="text-muted"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="mpesaCancelBtn">Cancel</button>
                <button type="button" class="btn btn-success" id="mpesaFinishBtn" style="display: none;">Complete Sale</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // API Headers Helper
    const apiHeaders = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
    };

    // Database Sync Functions
    async function loadCartFromDB() {
        try {
            const response = await fetch('/api/cart');
            if (response.ok) {
                cart = await response.json();
                updateCartUI();
            }
        } catch (e) {
            console.error('Error loading cart:', e);
        }
    }

    async function syncAddToCart(productId, quantity) {
        try {
            const response = await fetch('/api/cart', {
                method: 'POST',
                headers: apiHeaders,
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            });
            if (response.ok) {
                await loadCartFromDB();
            }
        } catch (e) {
            console.error('Error adding to cart:', e);
        }
    }

    async function syncUpdateQuantity(productId, quantity) {
        try {
            const response = await fetch(`/api/cart/${productId}`, {
                method: 'PUT',
                headers: apiHeaders,
                body: JSON.stringify({ quantity: quantity })
            });
            if (response.ok) {
                await loadCartFromDB();
            }
        } catch (e) {
            console.error('Error updating quantity:', e);
        }
    }

    async function syncRemoveItem(productId) {
        try {
            const response = await fetch(`/api/cart/${productId}`, {
                method: 'DELETE',
                headers: apiHeaders
            });
            if (response.ok) {
                await loadCartFromDB();
            }
        } catch (e) {
            console.error('Error removing item:', e);
        }
    }

    async function syncClearCart() {
        try {
            const response = await fetch('/api/cart', {
                method: 'DELETE',
                headers: apiHeaders
            });
            if (response.ok) {
                cart = [];
                updateCartUI();
            }
        } catch (e) {
            console.error('Error clearing cart:', e);
        }
    }

    // Product Search Functionality
    const productSearch = document.getElementById('productSearch');
    const searchResults = document.getElementById('searchResults');

    // Load state from DB and then products
    loadCartFromDB();
    loadAllProducts();

    function loadAllProducts() {
        fetch('{{ route("pos.products") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('All products loaded:', data);
                const products = Array.isArray(data) ? data : (data.data || []);
                if (products.length > 0) {
                    searchResults.innerHTML = '<div class="alert alert-info">Showing ' + products.length + ' available products. Start typing to search...</div>';
                    displaySearchResults(products);
                } else {
                    searchResults.innerHTML = '<div class="alert alert-warning">No products available</div>';
                }
            })
            .catch(error => {
                console.error('Load products error:', error);
                searchResults.innerHTML = '<div class="alert alert-danger">Error loading products: ' + error.message + '</div>';
            });
    }

    productSearch.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 1) {
            loadAllProducts();
            return;
        }

        // Search products
        fetch(`{{ route("pos.search") }}?q=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Search results:', data);
                const products = Array.isArray(data) ? data : (data.data || []);
                displaySearchResults(products);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div class="alert alert-danger">Error searching products: ' + error.message + '</div>';
            });
    });

    function displaySearchResults(products) {
        // Filter out products with 0 stock
        const availableProducts = products.filter(product => (product.stock || 0) > 0);
        
        if (availableProducts.length === 0) {
            searchResults.innerHTML = '<div class="alert alert-info">No available products found</div>';
            return;
        }

        let html = '<div class="list-group">';
        availableProducts.forEach((product, idx) => {
            html += `
                <div class="list-group-item product-item-container" data-id="${product.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <strong>${product.name}</strong><br>
                            <small class="text-muted">Code: ${product.code || 'N/A'} | IMEI: ${product.imei || 'N/A'} | Stock: ${product.stock || 0}</small>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <strong>KES ${parseFloat(product.price).toFixed(2)}</strong>
                            <input type="number" class="form-control form-control-sm qty-selector" 
                                   data-index="${idx}" value="1" min="1" max="${product.stock || 999}" 
                                   style="width: 60px;" placeholder="Qty">
                            <button type="button" class="btn btn-sm btn-success add-to-cart-btn" 
                                    data-id="${product.id}" data-name="${product.name}" 
                                    data-price="${product.price}">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        searchResults.innerHTML = html;

        // Add click handlers to add-to-cart buttons
        document.querySelectorAll('.add-to-cart-btn').forEach((btn, idx) => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const container = this.closest('.product-item-container');
                const qtyInput = container.querySelector('.qty-selector');
                const quantity = parseInt(qtyInput.value) || 1;
                
                syncAddToCart(this.dataset.id, quantity);
                
                // Reset quantity but keep search results
                qtyInput.value = '1';
                // productSearch.value = ''; // Don't clear search
                // loadAllProducts(); // Don't reload everything
            });
        });

        // Add Enter key support to qty-selectors
        document.querySelectorAll('.qty-selector').forEach(input => {
            input.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    const container = this.closest('.product-item-container');
                    const btn = container.querySelector('.add-to-cart-btn');
                    btn.click();
                }
            });
        });
    }

    // Update Cart UI Display
    function updateCartUI() {
        const cartItems = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        
        if (cart.length === 0) {
            cartItems.innerHTML = '';
            emptyCart.style.display = 'table-row';
            updateTotals();
            return;
        }
        
        emptyCart.style.display = 'none';
        
        let html = '';
        cart.forEach((item, index) => {
            const total = item.price * item.quantity;
            html += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.description || '-'}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm qty-input" 
                               data-id="${item.id}" value="${item.quantity}" min="1">
                    </td>
                    <td>KES ${item.price.toFixed(2)}</td>
                    <td>KES ${total.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item" data-id="${item.id}">
                            ×
                        </button>
                    </td>
                </tr>
            `;
        });
        
        cartItems.innerHTML = html;
        
        // Add event listeners
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                const productId = this.dataset.id;
                const newQty = parseInt(this.value) || 1;
                syncUpdateQuantity(productId, newQty);
            });
        });
        
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.dataset.id);
                syncRemoveItem(productId);
            });
        });
        
        updateTotals();
    }

    // Update Totals
    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const total = subtotal - discount;
        
        document.getElementById('subtotal').textContent = `KES ${subtotal.toFixed(2)}`;
        document.getElementById('totalAmount').textContent = `KES ${total.toFixed(2)}`;
        
        // Update hidden inputs
        document.getElementById('subtotalInput').value = subtotal.toFixed(2);
        document.getElementById('taxInput').value = '0';
        document.getElementById('totalInput').value = total.toFixed(2);
        document.getElementById('cartData').value = JSON.stringify(cart);
        
        calculateChange();
    }

    // Calculate Change
    function calculateChange() {
        const total = parseFloat(document.getElementById('totalInput').value) || 0;
        const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
        const change = tendered - total;
        
        document.getElementById('changeDue').textContent = `KES ${Math.max(0, change).toFixed(2)}`;
    }

    // Event Listeners
    document.getElementById('discount').addEventListener('input', updateTotals);
    document.getElementById('amountTendered').addEventListener('input', calculateChange);
    
    document.getElementById('clearCart').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear the cart?')) {
            syncClearCart();
        }
    });

    document.getElementById('cancelBtn').addEventListener('click', function() {
        if (cart.length > 0 && !confirm('Are you sure you want to cancel? All items will be cleared.')) {
            return;
        }
        syncClearCart();
        document.getElementById('posForm').reset();
    });

    // Payment Method Change
    document.getElementById('paymentMethod').addEventListener('change', function() {
        const mpesaField = document.getElementById('mpesaPhoneField');
        const amountTenderedField = document.getElementById('amountTenderedField');
        const changeDueField = document.getElementById('changeDueField');
        
        if (this.value === 'mpesa') {
            mpesaField.style.display = 'block';
            document.getElementById('mpesaPhone').required = true;
            // Hide amount tendered and change for M-Pesa
            amountTenderedField.style.display = 'none';
            changeDueField.style.display = 'none';
        } else {
            mpesaField.style.display = 'none';
            document.getElementById('mpesaPhone').required = false;
            amountTenderedField.style.display = 'block';
            changeDueField.style.display = 'block';
        }
    });

    // Form Submission
    document.getElementById('posForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (cart.length === 0) {
            alert('Please add items to the cart before completing the sale.');
            return;
        }
        
        const paymentMethod = document.getElementById('paymentMethod').value;
        const total = parseFloat(document.getElementById('totalInput').value) || 0;
        
        // Handle M-Pesa Payment
        if (paymentMethod === 'mpesa') {
            const phone = document.getElementById('mpesaPhone').value.trim();
            
            if (!phone) {
                alert('Please enter M-Pesa phone number');
                return;
            }
            
            // Initiate M-Pesa STK Push
            await initiateMpesaPayment(phone, total);
            return;
        }
        
        // For other payment methods, validate amount tendered
        const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
        
        if (tendered < total) {
            alert('Amount tendered is less than total amount.');
            return;
        }
        
        // Submit the form normally
        this.submit();
    });
    
    // M-Pesa Payment Functions
    let mpesaCheckoutRequestId = null;
    let mpesaPollingInterval = null;
    
    async function initiateMpesaPayment(phone, amount) {
        try {
            const modal = new bootstrap.Modal(document.getElementById('mpesaModal'));
            showMpesaStatus('pending');
            modal.show();
            
            document.getElementById('mpesaPhoneDisplay').textContent = `Phone: ${phone}`;
            document.getElementById('mpesaAmountDisplay').textContent = `Amount: KES ${amount.toFixed(2)}`;
            
            // Make the actual API call to initiate STK Push
 const response = await fetch('/api/mpesa/initiate', {
    method: 'POST',
    credentials: 'include',  // ← THIS IS CRITICAL
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        phone_number: phone,
        amount: amount
    })
});

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            // Check if response is HTML (error page)
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                const htmlResponse = await response.text();
                console.error('Received HTML response instead of JSON:', htmlResponse.substring(0, 500));
                showMpesaStatus('failed');
                document.getElementById('mpesaErrorMessage').textContent = 'Server error: API returned HTML. Check browser console for details.';
                return;
            }

            const data = await response.json();
            console.log('M-Pesa Response:', data);

            if (data.success) {
                console.log('STK Push initiated:', data);
                mpesaCheckoutRequestId = data.data.checkout_request_id;
                
                // Poll for payment status
                pollMpesaStatus(mpesaCheckoutRequestId);
            } else {
                console.error('STK Push failed:', data.message);
                showMpesaStatus('failed');
                document.getElementById('mpesaErrorMessage').textContent = data.message || 'Failed to initiate payment';
                if (data.debug_response) {
                    console.error('Debug response:', data.debug_response);
                }
            }
            
        } catch (error) {
            console.error('M-Pesa Error:', error);
            showMpesaStatus('failed');
            document.getElementById('mpesaErrorMessage').textContent = 'Error: ' + error.message;
        }
    }

    async function pollMpesaStatus(checkoutRequestId) {
        // Poll every 3 seconds for payment status
        mpesaPollingInterval = setInterval(async () => {
            try {
                const response = await fetch(`/api/mpesa/status/${checkoutRequestId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    if (data.status === 'confirmed') {
                        clearInterval(mpesaPollingInterval);
                        showMpesaStatus('success');
                        document.getElementById('mpesaReceiptNumber').textContent = 'Transaction ID: ' + (data.data.transaction_code || 'Confirmed');
                        document.getElementById('mpesaFinishBtn').style.display = 'block';
                    } else if (data.status === 'failed') {
                        clearInterval(mpesaPollingInterval);
                        showMpesaStatus('failed');
                        document.getElementById('mpesaErrorMessage').textContent = 'Payment was declined';
                    }
                }
            } catch (error) {
                console.error('Status polling error:', error);
            }
        }, 3000);
    }
    
    function showMpesaStatus(status) {
        document.getElementById('mpesaStatusPending').style.display = status === 'pending' ? 'block' : 'none';
        document.getElementById('mpesaStatusSuccess').style.display = status === 'success' ? 'block' : 'none';
        document.getElementById('mpesaStatusFailed').style.display = status === 'failed' ? 'block' : 'none';
    }
    
    // M-Pesa Modal Handlers
    document.getElementById('mpesaCancelBtn').addEventListener('click', function() {
        if (mpesaPollingInterval) {
            clearInterval(mpesaPollingInterval);
        }
        bootstrap.Modal.getInstance(document.getElementById('mpesaModal')).hide();
    });
    
    document.getElementById('mpesaFinishBtn').addEventListener('click', function() {
        // Set M-Pesa as payment method and submit form
        document.getElementById('amountTendered').value = document.getElementById('totalInput').value;
        document.getElementById('posForm').submit();
    });
});
</script>
@endpush