<?php

namespace App\Traits;

use App\Models\Scopes\CompanyScope as ModelCompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait CompanyScope
{
    /**
     * Boot the CompanyScope trait for a model.
     * 
     * Automatically filters all queries by the current session's company_id
     * and auto-injects company_id when creating new records.
     *
     * @return void
     */
    protected static function bootCompanyScope()
    {
        // Add a class-based global scope so it can be disabled consistently.
        static::addGlobalScope(new ModelCompanyScope());

        // Auto-inject company_id when creating new records
        static::creating(function (Model $model) {
            if (!$model->company_id && session()->has('company_id')) {
                $model->company_id = session('company_id');
            }
        });
    }

    /**
     * Scope query to a specific company.
     * 
     * Usage: Model::forCompany($companyId)->get()
     *
     * @param Builder $query
     * @param int $companyId
     * @return Builder
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where($this->getTable() . '.company_id', $companyId);
    }

    /**
     * Scope query to all companies (bypass global scope).
     * 
     * Usage: Model::allCompanies()->get()
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAllCompanies(Builder $query): Builder
    {
        return $query->withoutGlobalScope(ModelCompanyScope::class);
    }

    /**
     * Get the company relationship.
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id', 'id');
    }

    /**
     * Check if model belongs to current company.
     */
    public function isOwnedByCurrentCompany(): bool
    {
        return $this->company_id === session('company_id');
    }

    /**
     * Check if model belongs to specified company.
     */
    public function isOwnedByCompany(int $companyId): bool
    {
        return $this->company_id === $companyId;
    }
}
