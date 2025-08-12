<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relaciones
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Métodos de utilidad
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'membership_expiry' => 'Vencimiento de Membresía',
            'loyalty_discount' => 'Descuento por Lealtad',
            'payment_reminder' => 'Recordatorio de Pago',
            'general' => 'General',
            default => 'Otro'
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'membership_expiry' => 'warning',
            'loyalty_discount' => 'success',
            'payment_reminder' => 'info',
            'general' => 'secondary',
            default => 'primary'
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'membership_expiry' => 'warning',
            'loyalty_discount' => 'card_giftcard',
            'payment_reminder' => 'payment',
            'general' => 'notifications',
            default => 'info'
        };
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
