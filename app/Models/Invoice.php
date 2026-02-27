<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'sale_id',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'amount_due',
        'notes',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updateStatus(): void
    {
        $this->amount_paid = $this->payments()->sum('amount');
        $this->amount_due = $this->total_amount - $this->amount_paid;

        if ($this->amount_due <= 0) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partially_paid';
        } elseif (now()->isAfter($this->due_date) && $this->status === 'pending') {
            $this->status = 'overdue';
        }

        $this->save();
    }

    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->save();
    }

    public function markAsCancelled(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function isOverdue(): bool
    {
        return now()->isAfter($this->due_date) && $this->status !== 'paid' && $this->status !== 'cancelled';
    }

    public function getDaysOverdueAttribute(): ?int
    {
        if (!$this->isOverdue()) {
            return null;
        }

        return now()->diffInDays($this->due_date);
    }
}

