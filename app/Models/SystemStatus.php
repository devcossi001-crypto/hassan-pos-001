<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemStatus extends Model
{
    protected $table = 'system_status';

    protected $fillable = [
        'is_active',
        'status_reason',
        'deactivated_by',
        'deactivated_at',
        'activated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_active' => 'boolean',
        'deactivated_at' => 'datetime',
        'activated_at' => 'datetime',
        'subscription_end_date' => 'datetime',
    ];

    public function deactivatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    /**
     * Get the current system status
     */
    public static function getCurrent(): self
    {
        return self::first() ?? self::create(['is_active' => true]);
    }

    /**
     * Check if system is active
     */
    public static function isActive(): bool
    {
        $status = self::getCurrent();
        return $status->is_active && $status->isSubscriptionValid();
    }

    /**
     * Check if subscription is valid
     */
    public function isSubscriptionValid(): bool
    {
        if (!$this->subscription_active || !$this->subscription_end_date) {
            return false;
        }
        return now()->isBefore($this->subscription_end_date);
    }

    /**
     * Get days remaining in subscription
     */
    public function getDaysRemaining(): int
    {
        if (!$this->subscription_end_date) {
            return 0;
        }
        return now()->diffInDays($this->subscription_end_date, false);
    }

    /**
     * Set subscription end date
     */
    public function setSubscriptionEndDate($months = 6): void
    {
        $this->update([
            'subscription_end_date' => now()->addMonths($months),
            'subscription_active' => true,
        ]);
    }

    /**
     * Deactivate the system
     */
    public function deactivate(User $user, string $reason = ''): void
    {
        $this->update([
            'is_active' => false,
            'status_reason' => $reason,
            'deactivated_by' => $user->id,
            'deactivated_at' => now(),
        ]);
    }

    /**
     * Activate the system
     */
    public function activate(): void
    {
        $this->update([
            'is_active' => true,
            'status_reason' => null,
            'deactivated_by' => null,
            'deactivated_at' => null,
            'activated_at' => now(),
        ]);
    }
}
