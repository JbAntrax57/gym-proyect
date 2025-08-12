<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stock' => 'integer',
        'price' => 'decimal:2',
    ];

    // Relaciones
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Métodos de utilidad
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Sin stock';
        } elseif ($this->stock <= 5) {
            return 'Stock bajo';
        }
        return 'En stock';
    }

    public function getStockColorAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'danger';
        } elseif ($this->stock <= 5) {
            return 'warning';
        }
        return 'success';
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function hasLowStock(int $threshold = 5): bool
    {
        return $this->stock <= $threshold && $this->stock > 0;
    }

    public function updateStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }

    public function addStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->where('stock', '<=', $threshold)
                    ->where('stock', '>', 0);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
