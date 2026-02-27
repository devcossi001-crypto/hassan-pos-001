<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\LogsActivity;

class Sale extends Model
{
    use LogsActivity;
    protected $fillable = [
        'receipt_number',
        'cashier_id',
        'customer_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'primary_payment_method',
        'cash_paid',
        'mpesa_paid',
        'card_paid',
        'change_amount',
        'notes',
        'shift_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cash_paid' => 'decimal:2',
        'mpesa_paid' => 'decimal:2',
        'card_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function mpesaPayments(): HasMany
    {
        return $this->hasMany(MpesaPayment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(Return_::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class);
    }

    public function getTotalQuantity(): int
    {
        return $this->items()->sum('quantity');
    }
}
