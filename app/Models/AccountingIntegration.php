<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingIntegration extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'accounting_integrations';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'integration_type',
        'integration_name',
        'status',
        'client_id',
        'client_secret',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'settings',
        'auto_sync_po',
        'auto_sync_invoices',
        'auto_sync_vendors',
        'auto_sync_items',
        'created_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'auto_sync_po' => 'boolean',
        'auto_sync_invoices' => 'boolean',
        'auto_sync_vendors' => 'boolean',
        'auto_sync_items' => 'boolean',
        'token_expires_at' => 'datetime',
        'settings' => 'array',
    ];

    protected $hidden = [
        'client_secret',
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the company that owns this integration.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get sync logs for this integration.
     */
    public function syncLogs()
    {
        return $this->hasMany(IntegrationSyncLog::class, 'integration_id', 'id');
    }

    /**
     * Get field mappings for this integration.
     */
    public function fieldMappings()
    {
        return $this->hasMany(IntegrationFieldMapping::class, 'integration_id', 'id');
    }

    /**
     * Check if the access token is expired or expiring soon.
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return true;
        }

        return $this->token_expires_at->isPast() || 
               $this->token_expires_at->diffInMinutes(now()) < 10;
    }

    /**
     * Scope for active integrations only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for specific integration type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('integration_type', $type);
    }

    /**
     * Get the integration display name with type.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->integration_name} (" . ucfirst($this->integration_type) . ")";
    }
}
