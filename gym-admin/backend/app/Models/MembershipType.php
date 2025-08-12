<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_days' => 'integer',
        'price' => 'decimal:2',
    ];

    // Relaciones
    public function clientMemberships(): HasMany
    {
        return $this->hasMany(ClientMembership::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Métodos de utilidad
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_days == 1) {
            return '1 día';
        } elseif ($this->duration_days == 7) {
            return '1 semana';
        } elseif ($this->duration_days == 30) {
            return '1 mes';
        } elseif ($this->duration_days == 365) {
            return '1 año';
        }
        
        return $this->duration_days . ' días';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDailyPriceAttribute(): float
    {
        return round($this->price / $this->duration_days, 2);
    }
}
