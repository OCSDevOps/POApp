<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // Only apply scope if company_id is explicitly set in session
        // This prevents issues during migrations and seeders
        if (session()->has('company_id') && session('company_id')) {
            $builder->where($model->getTable() . '.company_id', session('company_id'));
        }
    }

    /**
     * Get the current company ID from session or auth.
     *
     * @return int|null
     */
    protected function getCompanyId(): ?int
    {
        // First check session
        if (session()->has('company_id')) {
            return session('company_id');
        }

        // Then check authenticated user (but don't cause recursion)
        if (auth()->check() && isset(auth()->user()->company_id)) {
            return auth()->user()->company_id;
        }

        return null;
    }
}
