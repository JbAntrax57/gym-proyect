<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'duration',
        'price',
        'status',
        'description',
        'max_visits',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'max_visits' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipos y estados
    const TYPES = [
        'Semanal' => 'Semanal',
        'Mensual' => 'Mensual',
        'Visita' => 'Visita'
    ];

    const STATUSES = [
        'Activa' => 'Activa',
        'Inactiva' => 'Inactiva',
        'Suspendida' => 'Suspendida'
    ];

    // Scopes para consultas comunes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%");
        });
    }

    // Relaciones
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    // Accessors y Mutators
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedDurationAttribute()
    {
        if ($this->type === 'Visita') {
            return $this->max_visits . ' visitas';
        }
        return $this->duration . ' días';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Activa' => 'positive',
            'Inactiva' => 'negative',
            'Suspendida' => 'warning',
            default => 'grey'
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'Semanal' => 'blue',
            'Mensual' => 'green',
            'Visita' => 'orange',
            default => 'grey'
        };
    }
}
