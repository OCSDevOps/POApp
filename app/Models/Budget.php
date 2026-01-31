<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $table = 'budget_master';
    protected $primaryKey = 'budget_id';
    public $timestamps = false;

    protected $fillable = [
        'budget_project_id',
        'budget_cost_code_id',
        'budget_original_amount',
        'budget_revised_amount',
        'budget_committed_amount',
        'budget_spent_amount',
        'budget_fiscal_year',
        'budget_notes',
        'budget_status',
        'budget_created_by',
        'budget_created_at',
        'budget_modified_by',
        'budget_modified_at',
        'procore_budget_id',
        'company_id',
    ];

    /**
     * Boot method to apply global scope.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    protected $casts = [
        'budget_original_amount' => 'decimal:2',
        'budget_revised_amount' => 'decimal:2',
        'budget_committed_amount' => 'decimal:2',
        'budget_spent_amount' => 'decimal:2',
        'budget_created_at' => 'datetime',
        'budget_modified_at' => 'datetime',
    ];

    /**
     * Get the project for this budget.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'budget_project_id', 'proj_id');
    }

    /**
     * Get the cost code for this budget.
     */
    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'budget_cost_code_id', 'cc_id');
    }

    /**
     * Get the user who created this budget.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'budget_created_by', 'id');
    }

    /**
     * Get remaining budget (computed column in DB, but also as accessor)
     */
    public function getRemainingAmountAttribute()
    {
        return $this->budget_revised_amount - $this->budget_committed_amount - $this->budget_spent_amount;
    }

    /**
     * Get budget utilization percentage
     */
    public function getUtilizationPercentAttribute()
    {
        if ($this->budget_revised_amount == 0) return 0;
        return round((($this->budget_committed_amount + $this->budget_spent_amount) / $this->budget_revised_amount) * 100, 2);
    }

    /**
     * Check if budget is over
     */
    public function getIsOverBudgetAttribute()
    {
        return $this->remaining_amount < 0;
    }

    /**
     * Scope for active budgets
     */
    public function scopeActive($query)
    {
        return $query->where('budget_status', 1);
    }

    /**
     * Scope for filtering by project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('budget_project_id', $projectId);
    }

    /**
     * Scope for filtering by cost code
     */
    public function scopeByCostCode($query, $costCodeId)
    {
        return $query->where('budget_cost_code_id', $costCodeId);
    }

    /**
     * Scope for filtering by fiscal year
     */
    public function scopeByFiscalYear($query, $year)
    {
        return $query->where('budget_fiscal_year', $year);
    }

    /**
     * Scope for over budget items
     */
    public function scopeOverBudget($query)
    {
        return $query->whereRaw('budget_revised_amount < (budget_committed_amount + budget_spent_amount)');
    }

    /**
     * Check if a PO amount can be committed against this budget
     */
    public function canCommit($amount)
    {
        return $this->remaining_amount >= $amount;
    }

    /**
     * Commit an amount to this budget
     */
    public function commit($amount, $userId = null)
    {
        if (!$this->canCommit($amount)) {
            throw new \Exception("Insufficient budget. Available: {$this->remaining_amount}, Requested: {$amount}");
        }

        $this->budget_committed_amount += $amount;
        $this->budget_modified_by = $userId ?? auth()->id();
        $this->budget_modified_at = now();
        $this->save();

        return $this;
    }

    /**
     * Record spending against this budget
     */
    public function spend($amount, $userId = null)
    {
        $this->budget_spent_amount += $amount;
        $this->budget_modified_by = $userId ?? auth()->id();
        $this->budget_modified_at = now();
        $this->save();

        return $this;
    }

    /**
     * Release committed amount (e.g., when PO is cancelled)
     */
    public function releaseCommitment($amount, $userId = null)
    {
        $this->budget_committed_amount = max(0, $this->budget_committed_amount - $amount);
        $this->budget_modified_by = $userId ?? auth()->id();
        $this->budget_modified_at = now();
        $this->save();

        return $this;
    }

    /**
     * Get budget for a project and cost code
     */
    public static function getBudgetFor($projectId, $costCodeId, $fiscalYear = null)
    {
        $fiscalYear = $fiscalYear ?? date('Y');
        
        return self::where('budget_project_id', $projectId)
            ->where('budget_cost_code_id', $costCodeId)
            ->where('budget_fiscal_year', $fiscalYear)
            ->where('budget_status', 1)
            ->first();
    }
}
