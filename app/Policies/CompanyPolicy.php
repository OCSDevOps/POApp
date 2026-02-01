<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any companies.
     */
    public function viewAny(User $user): bool
    {
        // Only super admins can view all companies
        return $user->u_type == 1;
    }

    /**
     * Determine whether the user can view the company.
     */
    public function view(User $user, Company $company): bool
    {
        // Super admins can view any company
        // Regular users can only view their own company
        return $user->u_type == 1 || $user->company_id == $company->id;
    }

    /**
     * Determine whether the user can create companies.
     */
    public function create(User $user): bool
    {
        // Only super admins can create companies
        return $user->u_type == 1;
    }

    /**
     * Determine whether the user can update the company.
     */
    public function update(User $user, Company $company): bool
    {
        // Only super admins can update companies
        return $user->u_type == 1;
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        // Only super admins can delete companies
        return $user->u_type == 1;
    }

    /**
     * Determine whether the user can switch companies.
     */
    public function switch(User $user, Company $company): bool
    {
        // Only super admins can switch companies
        return $user->u_type == 1;
    }
}
