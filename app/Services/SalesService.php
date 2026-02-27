<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\MpesaPayment;
use Illuminate\Support\Str;

class SalesService
{
    public function createSale(array $data): Sale
    {
        $sale = Sale::create([
            'receipt_number' => $this->generateReceiptNumber(),
            'cashier_id' => $data['cashier_id'],
            'customer_id' => $data['customer_id'] ?? null,
            'status' => 'completed',
            'subtotal' => $data['subtotal'],
            'tax_amount' => $data['tax_amount'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'total_amount' => $data['total_amount'],
            'primary_payment_method' => $data['primary_payment_method'],
            'cash_paid' => $data['cash_paid'] ?? 0,
            'mpesa_paid' => $data['mpesa_paid'] ?? 0,
            'card_paid' => $data['card_paid'] ?? 0,
            'change_amount' => $data['change_amount'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'shift_id' => $data['shift_id'] ?? null,
        ]);

        // Add sale items and update inventory
        foreach ($data['items'] as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['line_total'],
                'discount_per_item' => $item['discount_per_item'] ?? 0,
            ]);

            // Reduce inventory
            $product = Product::find($item['product_id']);
            $product->quantity_in_stock -= $item['quantity'];
            
            // Deactivate trade-in items when stock reaches 0
            if ($product->origin === 'Trade In' && $product->quantity_in_stock <= 0) {
                $product->is_active = false;
                $product->quantity_in_stock = 0; // Ensure it's 0, not negative
            }
            
            $product->save();

            // Record stock movement
            StockMovement::create([
                'product_id' => $item['product_id'],
                'type' => 'sale',
                'quantity' => -$item['quantity'],
                'notes' => "Sale #{$sale->receipt_number}",
                'user_id' => $data['cashier_id'],
            ]);
        }

        // If M-PESA payment, create payment record
        if ($data['primary_payment_method'] === 'mpesa' && isset($data['mpesa_phone'])) {
            MpesaPayment::create([
                'sale_id' => $sale->id,
                'phone_number' => $data['mpesa_phone'],
                'amount' => $data['mpesa_paid'],
                'status' => 'pending',
            ]);
        }

        return $sale;
    }

    public function refundSale(Sale $sale, array $refundData): void
    {
        $sale->update(['status' => 'refunded']);

        // Refund items and restore inventory
        foreach ($refundData['items'] as $item) {
            $product = Product::find($item['product_id']);
            $product->quantity_in_stock += $item['quantity'];
            $product->save();

            // Record stock movement
            StockMovement::create([
                'product_id' => $item['product_id'],
                'type' => 'return',
                'quantity' => $item['quantity'],
                'notes' => "Refund for Sale #{$sale->receipt_number}",
                'user_id' => auth()->id(),
            ]);
        }
    }

    public function processMpesaPayment(MpesaPayment $payment, string $transactionCode): void
    {
        $payment->update([
            'transaction_code' => $transactionCode,
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function failMpesaPayment(MpesaPayment $payment, string $errorMessage): void
    {
        $payment->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage,
        ]);

        // Cancel the sale if only payment method
        if ($payment->sale && $payment->sale->mpesa_paid == $payment->amount && $payment->sale->cash_paid == 0) {
            $payment->sale->update(['status' => 'cancelled']);
        }
    }

    private function generateReceiptNumber(): string
    {
        $prefix = 'RCP-' . date('Ymd');
        $lastSale = Sale::where('receipt_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastSale ? intval(substr($lastSale->receipt_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getDailySales(\DateTime $date, ?int $cashierId = null): array
    {
        $query = Sale::whereDate('created_at', $date->format('Y-m-d'))
            ->where('status', 'completed');

        if ($cashierId) {
            $query->where('cashier_id', $cashierId);
        }

        $baseQuery = clone $query;

        return [
            'total_sales' => $baseQuery->sum('total_amount'),
            'transaction_count' => $baseQuery->count(),
            'cash_sales' => (clone $query)->sum('cash_paid'),
            'mpesa_sales' => (clone $query)->sum('mpesa_paid'),
            'card_sales' => (clone $query)->sum('card_paid'),
        ];
    }
}
