<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Get all products in this category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get active products in this category.
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('status', 'active');
    }

    /**
     * Check if category is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope active categories only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the total number of products in this category.
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }
}
