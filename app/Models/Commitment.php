<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commitment extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'commitment_master';
    protected $primaryKey = 'commit_id';
    public $timestamps = false;

    // Status constants
    const STATUS_DRAFT = 1;
    const STATUS_PENDING = 2;
    const STATUS_APPROVED = 3;
    const STATUS_ACTIVE = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_CANCELLED = 6;

    protected $fillable = [
        'commit_project_id',
        'commit_supplier_id',
        'commit_cost_code_id',
        'commit_number',
        'commit_title',
        'commit_description',
        'commit_original_value',
        'commit_approved_cos',
        'commit_pending_cos',
        'commit_invoiced_amount',
        'commit_paid_amount',
        'commit_start_date',
        'commit_end_date',
        'commit_status',
        'commit_created_by',
        'commit_created_at',
        'commit_modified_by',
        'commit_modified_at',
        'procore_commitment_id',
        'company_id',
    ];

    protected $casts = [
        'commit_original_value' => 'decimal:2',
        'commit_approved_cos' => 'decimal:2',
        'commit_pending_cos' => 'decimal:2',
        'commit_invoiced_amount' => 'decimal:2',
        'commit_paid_amount' => 'decimal:2',
        'commit_start_date' => 'date',
        'commit_end_date' => 'date',
        'commit_created_at' => 'datetime',
        'commit_modified_at' => 'datetime',
    ];

    /**
     * Get the project for this commitment.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'commit_project_id', 'proj_id');
    }

    /**
     * Get the supplier for this commitment.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'commit_supplier_id', 'sup_id');
    }

    /**
     * Get the cost code for this commitment.
     */
    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'commit_cost_code_id', 'cc_id');
    }

    /**
     * Get the user who created this commitment.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'commit_created_by', 'id');
    }

    /**
     * Get revised value (computed column in DB, but also as accessor)
     */
    public function getRevisedValueAttribute()
    {
        return $this->commit_original_value + $this->commit_approved_cos;
    }

    /**
     * Get remaining to invoice
     */
    public function getRemainingToInvoiceAttribute()
    {
        return $this->revised_value - $this->commit_invoiced_amount;
    }

    /**
     * Get remaining to pay
     */
    public function getRemainingToPayAttribute()
    {
        return $this->commit_invoiced_amount - $this->commit_paid_amount;
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
        return $statuses[$this->commit_status] ?? 'Unknown';
    }

    /**
     * Scope for active commitments
     */
    public function scopeActive($query)
    {
        return $query->where('commit_status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for filtering by project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('commit_project_id', $projectId);
    }

    /**
     * Scope for filtering by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('commit_supplier_id', $supplierId);
    }

    /**
     * Scope for filtering by cost code
     */
    public function scopeByCostCode($query, $costCodeId)
    {
        return $query->where('commit_cost_code_id', $costCodeId);
    }

    /**
     * Generate next commitment number
     */
    public static function generateCommitmentNumber()
    {
        $lastCommit = self::orderBy('commit_id', 'DESC')->first();
        $nextNumber = $lastCommit ? intval(substr($lastCommit->commit_number, 3)) + 1 : 1;
        return 'CMT' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Approve the commitment
     */
    public function approve($userId = null)
    {
        $this->commit_status = self::STATUS_APPROVED;
        $this->commit_modified_by = $userId ?? auth()->id();
        $this->commit_modified_at = now();
        $this->save();

        // Update budget committed amount
        $budget = Budget::getBudgetFor(
            $this->commit_project_id,
            $this->commit_cost_code_id
        );

        if ($budget) {
            $budget->commit($this->commit_original_value, $userId);
        }

        return $this;
    }

    /**
     * Activate the commitment
     */
    public function activate($userId = null)
    {
        $this->commit_status = self::STATUS_ACTIVE;
        $this->commit_modified_by = $userId ?? auth()->id();
        $this->commit_modified_at = now();
        $this->save();
        return $this;
    }

    /**
     * Add change order
     */
    public function addChangeOrder($amount, $approved = false, $userId = null)
    {
        if ($approved) {
            $this->commit_approved_cos += $amount;
        } else {
            $this->commit_pending_cos += $amount;
        }
        
        $this->commit_modified_by = $userId ?? auth()->id();
        $this->commit_modified_at = now();
        $this->save();

        return $this;
    }

    /**
     * Record invoice
     */
    public function recordInvoice($amount, $userId = null)
    {
        $this->commit_invoiced_amount += $amount;
        $this->commit_modified_by = $userId ?? auth()->id();
        $this->commit_modified_at = now();
        $this->save();

        return $this;
    }

    /**
     * Record payment
     */
    public function recordPayment($amount, $userId = null)
    {
        $this->commit_paid_amount += $amount;
        $this->commit_modified_by = $userId ?? auth()->id();
        $this->commit_modified_at = now();
        $this->save();

        // Update budget spent amount
        $budget = Budget::getBudgetFor(
            $this->commit_project_id,
            $this->commit_cost_code_id
        );

        if ($budget) {
            $budget->spend($amount, $userId);
        }

        return $this;
    }
}
