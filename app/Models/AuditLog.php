<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'user_id',
        'event_type',
        'auditable_type',
        'auditable_id',
        'ip_address',
        'user_agent',
        'request_method',
        'request_url',
        'old_values',
        'new_values',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * User that triggered the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Decode old values JSON to array safely.
     */
    public function getOldValuesDecodedAttribute(): array
    {
        return json_decode($this->old_values ?? '[]', true) ?: [];
    }

    /**
     * Decode new values JSON to array safely.
     */
    public function getNewValuesDecodedAttribute(): array
    {
        return json_decode($this->new_values ?? '[]', true) ?: [];
    }

    /**
     * Decode metadata JSON to array safely.
     */
    public function getMetaDecodedAttribute(): array
    {
        return json_decode($this->meta ?? '[]', true) ?: [];
    }
}
