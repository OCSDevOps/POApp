<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'subdomain',
        'status',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get company_id accessor for compatibility.
     */
    public function getCompanyIdAttribute()
    {
        return $this->id;
    }

    /**
     * Get company_name accessor for compatibility.
     */
    public function getCompanyNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get all users belonging to this company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    /**
     * Get all projects belonging to this company.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'company_id', 'id');
    }

    /**
     * Get all suppliers belonging to this company.
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class, 'company_id', 'id');
    }

    /**
     * Get all purchase orders belonging to this company.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'company_id', 'id');
    }

    /**
     * Get all receive orders belonging to this company.
     */
    public function receiveOrders(): HasMany
    {
        return $this->hasMany(ReceiveOrder::class, 'company_id', 'id');
    }

    /**
     * Get all items belonging to this company.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'company_id', 'id');
    }

    /**
     * Get all budgets belonging to this company.
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'company_id', 'id');
    }

    /**
     * Get all cost codes belonging to this company.
     */
    public function costCodes(): HasMany
    {
        return $this->hasMany(CostCode::class, 'company_id', 'id');
    }

    /**
     * Get all budget change orders belonging to this company.
     */
    public function budgetChangeOrders(): HasMany
    {
        return $this->hasMany(BudgetChangeOrder::class, 'company_id', 'id');
    }

    /**
     * Get all PO change orders belonging to this company.
     */
    public function poChangeOrders(): HasMany
    {
        return $this->hasMany(PoChangeOrder::class, 'company_id', 'id');
    }

    /**
     * Get all approval workflows belonging to this company.
     */
    public function approvalWorkflows(): HasMany
    {
        return $this->hasMany(ApprovalWorkflow::class, 'company_id', 'id');
    }

    /**
     * Get all accounting integrations belonging to this company.
     */
    public function accountingIntegrations(): HasMany
    {
        return $this->hasMany(AccountingIntegration::class, 'company_id', 'id');
    }

    /**
     * Scope: Active companies only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get setting value.
     */
    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set setting value.
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }
}
