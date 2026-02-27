<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'sale_id',
        'customer_name',
        'customer_phone',
        'customer_id_number',
        'total_amount',
        'paid_amount',
        'balance',
        'monthly_payment',
        'duration_months',
        'start_date',
        'expected_end_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'duration_months' => 'integer',
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function isFullyPaid(): bool
    {
        return $this->balance <= 0;
    }

    public function paymentProgress(): float
    {
        if ($this->total_amount == 0) return 0;
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                     ->whereNotNull('expected_end_date')
                     ->where('expected_end_date', '<', now())
                     ->where('balance', '>', 0);
    }
}
