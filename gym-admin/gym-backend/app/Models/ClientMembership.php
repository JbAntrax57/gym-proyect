<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ClientMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'membership_type_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'amount_paid'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    // Relaciones
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    // Métodos de utilidad
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date >= now()->toDateString();
    }

    public function isExpired(): bool
    {
        return $this->end_date < now()->toDateString();
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->status === 'active' && 
               $this->end_date->diffInDays(now(), false) <= $days &&
               $this->end_date->diffInDays(now(), false) > 0;
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return max(0, $this->end_date->diffInDays(now(), false));
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->isExpired()) {
            return 'danger';
        } elseif ($this->isExpiringSoon()) {
            return 'warning';
        } elseif ($this->isActive()) {
            return 'success';
        }
        return 'secondary';
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount_paid, 2);
    }

    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date->format('d/m/Y');
    }

    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date->format('d/m/Y');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>=', now()->toDateString());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now()->toDateString());
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>=', now()->toDateString())
                    ->where('end_date', '<=', now()->addDays($days)->toDateString());
    }
}
