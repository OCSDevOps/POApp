<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CompanyScope;

class ProjectRole extends Model
{
    protected $table = 'project_roles';
    protected $primaryKey = 'role_id';
    
    protected $fillable = [
        'company_id',
        'project_id',
        'user_id',
        'role_name',
        'can_create_po',
        'can_approve_po',
        'can_create_budget_co',
        'can_approve_budget_co',
        'can_override_budget',
        'approval_limit',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'can_create_po' => 'boolean',
        'can_approve_po' => 'boolean',
        'can_create_budget_co' => 'boolean',
        'can_approve_budget_co' => 'boolean',
        'can_override_budget' => 'boolean',
        'approval_limit' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Role constants
    const ROLE_STAFF = 'Staff';
    const ROLE_PM = 'PM';
    const ROLE_MANAGER = 'Manager';
    const ROLE_DIRECTOR = 'Director';
    const ROLE_FINANCE = 'Finance';
    const ROLE_EXECUTIVE = 'Executive';
    const ROLE_ADMIN = 'Admin';

    /**
     * Boot the model and apply global scope.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    /**
     * Get the project this role belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'proj_id');
    }

    /**
     * Get the user for this role.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the company.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope to filter by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by role name.
     */
    public function scopeByRole($query, $roleName)
    {
        return $query->where('role_name', $roleName);
    }

    /**
     * Scope to get active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get users with specific permission.
     */
    public function scopeCanApprovePo($query)
    {
        return $query->where('can_approve_po', true);
    }

    /**
     * Scope to get users with budget override permission.
     */
    public function scopeCanOverrideBudget($query)
    {
        return $query->where('can_override_budget', true);
    }

    /**
     * Check if user has permission within their approval limit.
     */
    public function canApproveAmount($amount): bool
    {
        if (!$this->can_approve_po) {
            return false;
        }
        
        // Null limit = unlimited
        if ($this->approval_limit === null) {
            return true;
        }
        
        return $amount <= $this->approval_limit;
    }

    /**
     * Get all available roles.
     */
    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_STAFF => 'Staff - Can view, no approvals',
            self::ROLE_PM => 'Project Manager - Create POs/COs, manage project',
            self::ROLE_MANAGER => 'Manager - Approve up to limit',
            self::ROLE_DIRECTOR => 'Director - Approve large amounts',
            self::ROLE_FINANCE => 'Finance Team - Budget oversight, approvals',
            self::ROLE_EXECUTIVE => 'Executive - Final approval authority',
            self::ROLE_ADMIN => 'Admin - Full permissions, overrides',
        ];
    }

    /**
     * Get default permissions for a role.
     */
    public static function getDefaultPermissions($roleName): array
    {
        return match($roleName) {
            self::ROLE_STAFF => [
                'can_create_po' => false,
                'can_approve_po' => false,
                'can_create_budget_co' => false,
                'can_approve_budget_co' => false,
                'can_override_budget' => false,
                'approval_limit' => null,
            ],
            self::ROLE_PM => [
                'can_create_po' => true,
                'can_approve_po' => false,
                'can_create_budget_co' => true,
                'can_approve_budget_co' => false,
                'can_override_budget' => false,
                'approval_limit' => null,
            ],
            self::ROLE_MANAGER => [
                'can_create_po' => true,
                'can_approve_po' => true,
                'can_create_budget_co' => true,
                'can_approve_budget_co' => true,
                'can_override_budget' => false,
                'approval_limit' => 5000.00,
            ],
            self::ROLE_DIRECTOR => [
                'can_create_po' => true,
                'can_approve_po' => true,
                'can_create_budget_co' => true,
                'can_approve_budget_co' => true,
                'can_override_budget' => false,
                'approval_limit' => 25000.00,
            ],
            self::ROLE_FINANCE => [
                'can_create_po' => false,
                'can_approve_po' => true,
                'can_create_budget_co' => false,
                'can_approve_budget_co' => true,
                'can_override_budget' => true,
                'approval_limit' => null,
            ],
            self::ROLE_EXECUTIVE => [
                'can_create_po' => true,
                'can_approve_po' => true,
                'can_create_budget_co' => true,
                'can_approve_budget_co' => true,
                'can_override_budget' => true,
                'approval_limit' => null,
            ],
            self::ROLE_ADMIN => [
                'can_create_po' => true,
                'can_approve_po' => true,
                'can_create_budget_co' => true,
                'can_approve_budget_co' => true,
                'can_override_budget' => true,
                'approval_limit' => null,
            ],
            default => [
                'can_create_po' => false,
                'can_approve_po' => false,
                'can_create_budget_co' => false,
                'can_approve_budget_co' => false,
                'can_override_budget' => false,
                'approval_limit' => null,
            ],
        };
    }
}
