<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\InvoiceController;
use App\Models\Role;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Authentication routes (Laravel provides these by default)
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Shifts
    Route::post('shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::post('shifts/{shift}/close', [ShiftController::class, 'close'])->name('shifts.close');
    Route::get('shifts/active', [ShiftController::class, 'getActive'])->name('shifts.active');

    // Products
    // Products - Block access for cashiers
    Route::middleware([\App\Http\Middleware\RestrictInventoryAccess::class])->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('products/stock/add', [ProductController::class, 'addStock'])->name('stock.add');
    });

    // Sales
    Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
    Route::get('sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('sales.receipt');
    
    // POS Product Search Routes (JSON endpoints for AJAX)
    Route::get('pos/products', [SalesController::class, 'getProducts'])->name('pos.products');
    Route::get('pos/products/search', [SalesController::class, 'searchProduct'])->name('pos.search');

    // Expenses & Finance
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    Route::patch('expenses/{expense}/approve', [\App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::patch('expenses/{expense}/reject', [\App\Http\Controllers\ExpenseController::class, 'reject'])->name('expenses.reject');
    Route::resource('expense-categories', \App\Http\Controllers\ExpenseCategoryController::class);

    // Purchases
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);
    Route::patch('purchase-orders/{purchaseOrder}/receive', [\App\Http\Controllers\PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    // Reports
    Route::get('reports/sales', [\App\Http\Controllers\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/pnl', [\App\Http\Controllers\ReportController::class, 'profitLoss'])->name('reports.pnl');

    // Loans / Credit Sales - Block access for cashiers
    Route::middleware([\App\Http\Middleware\RestrictInventoryAccess::class])->group(function () {
        Route::resource('loans', \App\Http\Controllers\LoanController::class);
        Route::post('loans/{loan}/payment', [\App\Http\Controllers\LoanController::class, 'addPayment'])->name('loans.payment');
        Route::put('loans/{loan}/defaulted', [\App\Http\Controllers\LoanController::class, 'markDefaulted'])->name('loans.defaulted');
    });

    // Invoices - Accessible to all authenticated users (including cashiers)
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');

    // Trade-Ins - Accessible to all authenticated users
    Route::resource('trade-ins', \App\Http\Controllers\TradeInController::class);


    // Users
    Route::resource('users', UserController::class);

    // Activity Logs (Super Admin only)
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // System Management (Owner only)
    Route::prefix('system')->name('system.')->middleware(\App\Http\Middleware\OnlyOwner::class)->group(function () {
        Route::get('status', [SystemController::class, 'status'])->name('status');
        Route::post('activate', [SystemController::class, 'activate'])->name('activate');
        Route::post('deactivate', [SystemController::class, 'deactivate'])->name('deactivate');
        Route::post('subscription/update', [SystemController::class, 'updateSubscription'])->name('subscription.update');
    });

    // Logout
    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});