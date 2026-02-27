<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\MpesaController;
use App\Http\Controllers\Api\CartController;
use Illuminate\Support\Facades\Route;

// Cart routes - accessible to both authenticated and guest users
// Using auth:sanctum middleware which is session-aware due to EnsureFrontendRequestsAreStateful
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'store']);
        Route::put('/{productId}', [CartController::class, 'update']);
        Route::delete('/{productId}', [CartController::class, 'destroy']);
        Route::delete('/', [CartController::class, 'clear']);
    });
});

// Product routes - public read access
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search/query', [ProductController::class, 'search']);
    Route::get('/barcode/lookup', [ProductController::class, 'byBarcode']);
    Route::get('/inventory/low-stock', [ProductController::class, 'lowStock']);
    Route::get('/inventory/stock-value', [ProductController::class, 'stockValue']);
    Route::get('/{product}', [ProductController::class, 'show']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Product Management (Protected actions)
    Route::prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{product}', [ProductController::class, 'update']);
    });

    // Sales Management
    Route::prefix('sales')->group(function () {
        Route::post('/', [SalesController::class, 'store']);
        Route::get('/{sale}', [SalesController::class, 'show']);
        Route::get('/daily/{date?}', [SalesController::class, 'dailySales']);
        Route::get('/cashier/summary', [SalesController::class, 'cashierSales']);
        Route::post('/{sale}/return', [SalesController::class, 'processReturn']);
        Route::post('/inventory/seed-test-data', [SalesController::class, 'seedTestData']);
    });

    // Shift Management
    Route::prefix('shifts')->group(function () {
        Route::post('/open', [ShiftController::class, 'openShift']);
        Route::post('/close', [ShiftController::class, 'closeShift']);
        Route::get('/current', [ShiftController::class, 'currentShift']);
        Route::get('/{shift}/summary', [ShiftController::class, 'summary']);
        Route::get('/history', [ShiftController::class, 'shiftHistory']);
    });

    // Financial Management
    Route::prefix('finance')->group(function () {
        Route::post('/expenses', [FinanceController::class, 'recordExpense']);
        Route::post('/expenses/{expense}/approve', [FinanceController::class, 'approveExpense']);
        Route::post('/expenses/{expense}/reject', [FinanceController::class, 'rejectExpense']);
        Route::post('/income', [FinanceController::class, 'recordOtherIncome']);
        Route::get('/daily-summary', [FinanceController::class, 'dailyFinancialSummary']);
        Route::get('/profit-and-loss', [FinanceController::class, 'profitAndLoss']);
        Route::get('/expense-breakdown', [FinanceController::class, 'expenseBreakdown']);
        Route::get('/monthly-trend', [FinanceController::class, 'monthlyTrend']);
        Route::get('/pending-expenses', [FinanceController::class, 'pendingExpenses']);
    });

    // Purchase Management
    Route::prefix('purchases')->group(function () {
        Route::get('/suppliers', [PurchaseController::class, 'suppliers']);
        Route::post('/suppliers', [PurchaseController::class, 'createSupplier']);
        Route::post('/orders', [PurchaseController::class, 'createPurchaseOrder']);
        Route::post('/orders/{po}/receive', [PurchaseController::class, 'receivePurchaseOrder']);
        Route::post('/orders/{po}/payment', [PurchaseController::class, 'recordPayment']);
        Route::get('/orders', [PurchaseController::class, 'purchaseOrders']);
        Route::get('/orders/pending', [PurchaseController::class, 'pendingOrders']);
        Route::get('/orders/{po}', [PurchaseController::class, 'purchaseOrderDetail']);
        Route::get('/suppliers/{supplier}/balance', [PurchaseController::class, 'supplierBalance']);
    });

    // M-Pesa Payment Integration
    Route::prefix('mpesa')->group(function () {
        Route::post('/initiate', [MpesaController::class, 'initiate']);
        Route::get('/status/{checkoutRequestId}', [MpesaController::class, 'status']);
    });
});

// M-Pesa Callback (No Auth Required - Called by Safaricom)
Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('api.mpesa.callback');