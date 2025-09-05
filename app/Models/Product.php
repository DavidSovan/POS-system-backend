<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'price',
        'cost',
        'stock',
        'reorder_level',
        'description',
        'barcode',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'stock' => 'integer',
            'reorder_level' => 'integer',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all stock movements for this product.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get recent stock movements.
     */
    public function recentStockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->orderBy('created_at', 'desc')->limit(10);
    }

    /**
     * Check if product is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if product needs reordering.
     */
    public function needsReordering(): bool
    {
        return $this->stock <= $this->reorder_level;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Get profit margin.
     */
    public function getProfitMarginAttribute(): ?float
    {
        if (!$this->cost || $this->cost <= 0) {
            return null;
        }
        
        return (($this->price - $this->cost) / $this->cost) * 100;
    }

    /**
     * Get profit amount.
     */
    public function getProfitAmountAttribute(): ?float
    {
        if (!$this->cost) {
            return null;
        }
        
        return $this->price - $this->cost;
    }

    /**
     * Adjust stock level and create movement record.
     */
    public function adjustStock(int $quantity, string $type, string $reason, ?string $notes = null, ?float $unitCost = null, ?string $reference = null): StockMovement
    {
        return DB::transaction(function () use ($quantity, $type, $reason, $notes, $unitCost, $reference) {
            // Update stock
            if ($type === 'in') {
                $this->increment('stock', $quantity);
            } else {
                $this->decrement('stock', $quantity);
            }

            $this->refresh();

            // Create stock movement record
            return $this->stockMovements()->create([
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'notes' => $notes,
                'unit_cost' => $unitCost,
                'user_id' => auth()->id(),
                'reference' => $reference,
                'stock_after' => $this->stock,
            ]);
        });
    }

    /**
     * Scope active products only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope products that need reordering.
     */
    public function scopeNeedsReordering($query)
    {
        return $query->whereRaw('stock <= reorder_level');
    }

    /**
     * Scope out of stock products.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    /**
     * Scope products by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Search products by name, SKU, or barcode.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }
}
