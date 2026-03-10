<?php

namespace App\Domain\Shared\Traits;

use App\Models\AuditLog;

/**
 * Trait Auditable
 *
 * Agrega auditoría automática a cualquier modelo Eloquent.
 * Registra creación, actualización, eliminación y restauración.
 *
 * Uso: `use Auditable;` en el modelo.
 *
 * Para excluir campos del log (ej. password):
 *   protected array $auditExclude = ['password', 'remember_token'];
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->logAudit('created', [], $model->getAuditableAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }

            $oldValues = [];
            $newValues = [];
            foreach ($dirty as $key => $value) {
                if ($model->isExcludedFromAudit($key)) {
                    continue;
                }
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $value;
            }

            if (!empty($newValues)) {
                $model->logAudit('updated', $oldValues, $newValues);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', $model->getAuditableAttributes(), []);
        });

        // Soporte para SoftDeletes: restauración
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logAudit('restored', [], $model->getAuditableAttributes());
            });
        }
    }

    /**
     * Registra una entrada en el audit log.
     */
    protected function logAudit(string $action, array $oldValues, array $newValues): void
    {
        $userId = null;
        $ipAddress = null;
        $userAgent = null;

        // Intentar obtener user_id del request actual (JWT middleware lo setea)
        if (app()->bound('request')) {
            $request = app('request');
            $userId = $request->attributes->get('user_id');
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent ? substr($userAgent, 0, 255) : null,
        ]);
    }

    /**
     * Retorna los atributos del modelo excluyendo los campos sensibles.
     */
    protected function getAuditableAttributes(): array
    {
        $attributes = $this->getAttributes();
        $excluded = $this->getAuditExcludedFields();

        return array_diff_key($attributes, array_flip($excluded));
    }

    /**
     * Verifica si un campo está excluido de la auditoría.
     */
    protected function isExcludedFromAudit(string $key): bool
    {
        return in_array($key, $this->getAuditExcludedFields(), true);
    }

    /**
     * Retorna los campos excluidos de la auditoría.
     * Se pueden definir en el modelo con: protected array $auditExclude = [...];
     */
    protected function getAuditExcludedFields(): array
    {
        $defaults = ['password', 'remember_token', 'updated_at', 'created_at', 'deleted_at'];
        $custom = property_exists($this, 'auditExclude') ? $this->auditExclude : [];

        return array_unique(array_merge($defaults, $custom));
    }

    /**
     * Relación polimórfica inversa: obtener los audit logs de esta entidad.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
