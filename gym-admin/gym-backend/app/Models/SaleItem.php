<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'membership_type_id',
        'quantity',
        'unit_price',
        'total_price',
        'discount_percentage'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Relaciones
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    // Métodos de utilidad
    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return '$' . number_format($this->total_price, 2);
    }

    public function getFormattedDiscountPercentageAttribute(): string
    {
        return $this->discount_percentage . '%';
    }

    public function getDiscountAmountAttribute(): float
    {
        return round(($this->unit_price * $this->quantity * $this->discount_percentage) / 100, 2);
    }

    public function getPriceBeforeDiscountAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }

    public function isProduct(): bool
    {
        return !is_null($this->product_id);
    }

    public function isMembership(): bool
    {
        return !is_null($this->membership_type_id);
    }

    public function getItemNameAttribute(): string
    {
        if ($this->isProduct()) {
            return $this->product->name;
        } elseif ($this->isMembership()) {
            return $this->membershipType->name;
        }
        return 'Item desconocido';
    }

    public function getItemTypeAttribute(): string
    {
        if ($this->isProduct()) {
            return 'Producto';
        } elseif ($this->isMembership()) {
            return 'Membresía';
        }
        return 'Otro';
    }
}
