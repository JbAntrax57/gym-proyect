<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'loyalty_points',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'loyalty_points' => 'integer',
    ];

    // Relaciones
    public function memberships(): HasMany
    {
        return $this->hasMany(ClientMembership::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function loyaltyDiscounts(): HasMany
    {
        return $this->hasMany(LoyaltyDiscount::class);
    }

    // Métodos de utilidad
    public function getActiveMembershipAttribute()
    {
        return $this->memberships()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->first();
    }

    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
    }

    public function hasActiveMembership(): bool
    {
        return $this->memberships()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->exists();
    }

    public function getMembershipExpiryDateAttribute()
    {
        $activeMembership = $this->activeMembership;
        return $activeMembership ? $activeMembership->end_date : null;
    }
}
