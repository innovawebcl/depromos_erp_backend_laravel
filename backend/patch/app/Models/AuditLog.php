<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * El usuario que realizó la acción.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * La entidad auditada (polimórfica).
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope para filtrar por tipo de entidad.
     */
    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('auditable_type', $modelClass);
    }

    /**
     * Scope para filtrar por acción.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
