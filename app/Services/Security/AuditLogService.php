<?php

namespace App\Services\Security;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditLogService
{
    /**
     * Keys that should never be stored in audit payloads.
     */
    private array $sensitiveKeys = [
        'password',
        'remember_token',
        'two_factor_secret',
        'token',
        'access_token',
        'refresh_token',
        'client_secret',
    ];

    /**
     * Log a generic security/business event.
     */
    public function logEvent(
        string $eventType,
        ?Model $model = null,
        array $oldValues = [],
        array $newValues = [],
        array $meta = []
    ): void {
        if (!Schema::hasTable('audit_logs')) {
            return;
        }

        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        $request = request();
        $user = Auth::user();

        $companyId = $meta['company_id']
            ?? ($model && isset($model->company_id) ? $model->company_id : null)
            ?? ($user->company_id ?? session('company_id'));

        $payload = [
            'company_id' => $companyId,
            'user_id' => $user->id ?? ($meta['user_id'] ?? null),
            'event_type' => $eventType,
            'auditable_type' => $model ? get_class($model) : ($meta['auditable_type'] ?? null),
            'auditable_id' => $model ? (string) $model->getKey() : ($meta['auditable_id'] ?? null),
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 512) : null,
            'request_method' => $request ? $request->method() : null,
            'request_url' => $request ? substr((string) $request->fullUrl(), 0, 1024) : null,
            'old_values' => $this->encode($oldValues),
            'new_values' => $this->encode($newValues),
            'meta' => $this->encode($meta),
            'created_at' => now(),
        ];

        DB::table('audit_logs')->insert($payload);
    }

    /**
     * Log Eloquent model create/update/delete events.
     */
    public function logModelEvent(string $eventName, Model $model, array $oldValues = [], array $newValues = []): void
    {
        $eventType = 'model.' . strtolower($eventName);
        $meta = [
            'model' => get_class($model),
            'table' => $model->getTable(),
        ];

        $this->logEvent(
            $eventType,
            $model,
            $this->sanitize($oldValues),
            $this->sanitize($newValues),
            $meta
        );
    }

    /**
     * Remove sensitive keys and normalize values for JSON serialization.
     */
    private function sanitize(array $values): array
    {
        $sanitized = [];

        foreach ($values as $key => $value) {
            if (in_array((string) $key, $this->sensitiveKeys, true)) {
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize($value);
                continue;
            }

            if ($value instanceof \DateTimeInterface) {
                $sanitized[$key] = $value->format(DATE_ATOM);
                continue;
            }

            if (is_bool($value) || is_numeric($value) || is_null($value)) {
                $sanitized[$key] = $value;
                continue;
            }

            if (is_object($value)) {
                $sanitized[$key] = method_exists($value, '__toString') ? (string) $value : json_encode($value);
                continue;
            }

            $sanitized[$key] = (string) $value;
        }

        return $sanitized;
    }

    /**
     * Encode values to JSON safely.
     */
    private function encode(array $values): ?string
    {
        $cleanValues = $this->sanitize($values);
        if (empty($cleanValues)) {
            return null;
        }

        return json_encode($cleanValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
