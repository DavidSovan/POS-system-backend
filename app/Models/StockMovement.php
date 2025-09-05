<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reason',
        'notes',
        'unit_cost',
        'user_id',
        'reference',
        'stock_after',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'stock_after' => 'integer',
        ];
    }

    /**
     * Get the product that owns the stock movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the stock movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is an inbound movement.
     */
    public function isInbound(): bool
    {
        return $this->type === 'in';
    }

    /**
     * Check if this is an outbound movement.
     */
    public function isOutbound(): bool
    {
        return $this->type === 'out';
    }

    /**
     * Get formatted quantity with direction.
     */
    public function getFormattedQuantityAttribute(): string
    {
        $prefix = $this->isInbound() ? '+' : '-';
        return $prefix . $this->quantity;
    }

    /**
     * Get total value of this movement.
     */
    public function getTotalValueAttribute(): ?float
    {
        if (!$this->unit_cost) {
            return null;
        }
        
        return $this->quantity * $this->unit_cost;
    }

    /**
     * Scope inbound movements only.
     */
    public function scopeInbound($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope outbound movements only.
     */
    public function scopeOutbound($query)
    {
        return $query->where('type', 'out');
    }

    /**
     * Scope movements by reason.
     */
    public function scopeByReason($query, string $reason)
    {
        return $query->where('reason', $reason);
    }

    /**
     * Scope movements by product.
     */
    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope movements by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
