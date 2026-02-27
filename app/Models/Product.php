<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;
    // Explicitly set the table name (good practice with SQL Server)
    protected $table = 'products';

    // If you're using non-incrementing or non-integer IDs (common in SQL Server migrations)
    // public $incrementing = false; // Uncomment if ID is UNIQUEIDENTIFIER (GUID)
    // protected $keyType = 'string'; // Uncomment if using GUID

    protected $fillable = [
        'name',
        'description',
        'sku',
        'imei',
        'barcode',
        'origin',
        'cost_price',
        'selling_price',
        'quantity_in_stock',
        'total_cost',
        'reorder_level',
        'category_id',
        'is_active',
    ];

    protected $casts = [
        'cost_price'      => 'decimal:2',
        'selling_price'   => 'decimal:2',
        'total_cost'      => 'decimal:2',
        'quantity_in_stock' => 'integer',
        'reorder_level'   => 'integer',
        'category_id'     => 'integer',
        'is_active'       => 'boolean',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Helper Methods
    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->reorder_level;
    }

    public function getProfitAttribute(): float
    {
        return $this->selling_price - $this->cost_price;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->selling_price == 0) {
            return 0.0;
        }

        return round((($this->selling_price - $this->cost_price) / $this->selling_price) * 100, 2);
    }

    // Optional: Scope for active products only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Optional: Scope for low stock
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity_in_stock <= reorder_level')->where('is_active', true);
    }

    /**
     * Generate a unique SKU for the product
     */
    public static function generateSku(): string
    {
        // Generate SKU as PRD-TIMESTAMP-RANDOM
        // Format: PRD-20260204090249-ABC123
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        return "PRD-{$timestamp}-{$random}";
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            // Auto-generate SKU if not provided
            if (empty($product->sku)) {
                $product->sku = self::generateSku();
            }
        });

        static::saving(function ($product) {
            $product->total_cost = $product->cost_price * $product->quantity_in_stock;
        });
    }
}