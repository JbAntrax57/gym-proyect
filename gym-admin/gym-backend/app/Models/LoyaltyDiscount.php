<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoyaltyDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'discount_percentage',
        'reason',
        'valid_until',
        'used',
        'used_at'
    ];

    protected $casts = [
        'valid_until' => 'date',
        'used' => 'boolean',
        'used_at' => 'datetime',
        'discount_percentage' => 'decimal:2',
    ];

    // Relaciones
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Métodos de utilidad
    public function getFormattedDiscountPercentageAttribute(): string
    {
        return $this->discount_percentage . '%';
    }

    public function getFormattedValidUntilAttribute(): string
    {
        return $this->valid_until->format('d/m/Y');
    }

    public function getFormattedUsedAtAttribute(): string
    {
        return $this->used_at ? $this->used_at->format('d/m/Y H:i') : 'No usado';
    }

    public function isExpired(): bool
    {
        return $this->valid_until < now()->toDateString();
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->used;
    }

    public function canBeUsed(): bool
    {
        return $this->isValid();
    }

    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'used_at' => now()
        ]);
    }

    public function getStatusAttribute(): string
    {
        if ($this->used) {
            return 'Usado';
        } elseif ($this->isExpired()) {
            return 'Expirado';
        } else {
            return 'Válido';
        }
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->used) {
            return 'secondary';
        } elseif ($this->isExpired()) {
            return 'danger';
        } else {
            return 'success';
        }
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return max(0, $this->valid_until->diffInDays(now(), false));
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->isValid() && $this->daysUntilExpiry <= $days;
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('used', false)
                    ->where('valid_until', '>=', now()->toDateString());
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now()->toDateString());
    }

    public function scopeUsed($query)
    {
        return $query->where('used', true);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('used', false)
                    ->where('valid_until', '>=', now()->toDateString())
                    ->where('valid_until', '<=', now()->addDays($days)->toDateString());
    }
}
