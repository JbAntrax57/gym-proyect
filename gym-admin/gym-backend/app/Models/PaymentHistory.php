<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_membership_id',
        'amount',
        'payment_date',
        'payment_type',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relaciones
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function clientMembership(): BelongsTo
    {
        return $this->belongsTo(ClientMembership::class);
    }

    // Métodos de utilidad
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getFormattedPaymentDateAttribute(): string
    {
        return $this->payment_date->format('d/m/Y');
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'membership' => 'Membresía',
            'product' => 'Producto',
            default => 'Otro'
        };
    }

    public function isMembershipPayment(): bool
    {
        return $this->payment_type === 'membership';
    }

    public function isProductPayment(): bool
    {
        return $this->payment_type === 'product';
    }

    // Scopes
    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('payment_date', now()->year);
    }
}
