<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationSyncLog extends Model
{
    use HasFactory;

    protected $table = 'integration_sync_logs';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'integration_id',
        'company_id',
        'sync_type',
        'operation',
        'status',
        'entity_type',
        'entity_id',
        'external_id',
        'records_attempted',
        'records_succeeded',
        'records_failed',
        'error_message',
        'error_details',
        'started_at',
        'completed_at',
        'duration_seconds',
    ];

    protected $casts = [
        'records_attempted' => 'integer',
        'records_succeeded' => 'integer',
        'records_failed' => 'integer',
        'error_details' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    /**
     * Get the integration that owns this log.
     */
    public function integration()
    {
        return $this->belongsTo(AccountingIntegration::class, 'integration_id', 'id');
    }

    /**
     * Get the company that owns this log.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Scope for successful syncs.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed syncs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for recent logs.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get success rate as percentage.
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->records_attempted === 0) {
            return 0;
        }

        return round(($this->records_succeeded / $this->records_attempted) * 100, 2);
    }
}
