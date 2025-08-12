<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'total_amount',
        'payment_method',
        'sale_date',
        'items_count',
        'discount_amount',
        'final_amount'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'items_count' => 'integer',
    ];

    // Relaciones
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Métodos de utilidad
    public function getFormattedTotalAmountAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return '$' . number_format($this->discount_amount, 2);
    }

    public function getFormattedFinalAmountAttribute(): string
    {
        return '$' . number_format($this->final_amount, 2);
    }

    public function getFormattedSaleDateAttribute(): string
    {
        return $this->sale_date->format('d/m/Y H:i');
    }

    public function getDiscountPercentageAttribute(): float
    {
        if ($this->total_amount > 0) {
            return round(($this->discount_amount / $this->total_amount) * 100, 2);
        }
        return 0;
    }

    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    public function isClientSale(): bool
    {
        return !is_null($this->client_id);
    }

    // Scopes
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeWithDiscount($query)
    {
        return $query->where('discount_amount', '>', 0);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sale_date', now()->month)
                    ->whereYear('sale_date', now()->year);
    }
}
