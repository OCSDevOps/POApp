<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCode extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'cost_code_master';
    protected $primaryKey = 'cc_id';
    public $timestamps = false;

    protected $fillable = [
        'cc_no',
        'cc_name',
        'cc_description',
        'cc_parent_code',
        'cc_category_code',
        'cc_subcategory_code',
        'cc_level',
        'cc_full_code',
        'cc_status',
        'cc_created_by',
        'cc_created_at',
        'cc_modifyby',
        'cc_modifydate',
        'company_id',
    ];

    protected $casts = [
        'cc_status' => 'integer',
        'cc_level' => 'integer',
        'cc_created_at' => 'datetime',
        'cc_modifydate' => 'datetime',
    ];

    // Level constants
    const LEVEL_PARENT = 1;
    const LEVEL_CATEGORY = 2;
    const LEVEL_SUBCATEGORY = 3;

    /**
     * Get the items for the cost code.
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'item_ccode_ms', 'cc_id');
    }

    /**
     * Get parent cost code (level 1).
     */
    public function parent()
    {
        if ($this->cc_level == self::LEVEL_PARENT) {
            return null;
        }
        
        return self::where('cc_parent_code', $this->cc_parent_code)
            ->where('cc_level', self::LEVEL_PARENT)
            ->where('cc_status', 1)
            ->first();
    }

    /**
     * Get category cost code (level 2).
     */
    public function category()
    {
        if ($this->cc_level <= self::LEVEL_CATEGORY) {
            return null;
        }
        
        return self::where('cc_parent_code', $this->cc_parent_code)
            ->where('cc_category_code', $this->cc_category_code)
            ->where('cc_level', self::LEVEL_CATEGORY)
            ->where('cc_status', 1)
            ->first();
    }

    /**
     * Get child cost codes.
     */
    public function children()
    {
        if ($this->cc_level == self::LEVEL_PARENT) {
            // Get all categories under this parent
            return self::where('cc_parent_code', $this->cc_parent_code)
                ->where('cc_level', self::LEVEL_CATEGORY)
                ->where('cc_status', 1)
                ->get();
        } elseif ($this->cc_level == self::LEVEL_CATEGORY) {
            // Get all subcategories under this category
            return self::where('cc_parent_code', $this->cc_parent_code)
                ->where('cc_category_code', $this->cc_category_code)
                ->where('cc_level', self::LEVEL_SUBCATEGORY)
                ->where('cc_status', 1)
                ->get();
        }
        
        return collect();
    }

    /**
     * Get all descendants (recursive).
     */
    public function descendants()
    {
        $descendants = collect();
        
        if ($this->cc_level == self::LEVEL_PARENT) {
            // Get all categories and subcategories
            $categories = self::where('cc_parent_code', $this->cc_parent_code)
                ->where('cc_level', self::LEVEL_CATEGORY)
                ->where('cc_status', 1)
                ->get();
            
            $descendants = $descendants->merge($categories);
            
            $subcategories = self::where('cc_parent_code', $this->cc_parent_code)
                ->where('cc_level', self::LEVEL_SUBCATEGORY)
                ->where('cc_status', 1)
                ->get();
            
            $descendants = $descendants->merge($subcategories);
        } elseif ($this->cc_level == self::LEVEL_CATEGORY) {
            // Get all subcategories
            $subcategories = self::where('cc_parent_code', $this->cc_parent_code)
                ->where('cc_category_code', $this->cc_category_code)
                ->where('cc_level', self::LEVEL_SUBCATEGORY)
                ->where('cc_status', 1)
                ->get();
            
            $descendants = $descendants->merge($subcategories);
        }
        
        return $descendants;
    }

    /**
     * Get rollup of budgets for this cost code and descendants.
     */
    public function getRollupBudget($projectId)
    {
        $costCodeIds = collect([$this->cc_id]);
        $costCodeIds = $costCodeIds->merge($this->descendants()->pluck('cc_id'));
        
        return Budget::whereIn('budget_cc_ms', $costCodeIds)
            ->where('budget_project_ms', $projectId)
            ->selectRaw('
                SUM(budget_amount) as total_budget,
                SUM(budget_committed) as total_committed,
                SUM(budget_actual) as total_actual
            ')
            ->first();
    }

    /**
     * Scope for parent codes only.
     */
    public function scopeParents($query)
    {
        return $query->where('cc_level', self::LEVEL_PARENT);
    }

    /**
     * Scope for category codes only.
     */
    public function scopeCategories($query)
    {
        return $query->where('cc_level', self::LEVEL_CATEGORY);
    }

    /**
     * Scope for subcategory codes only.
     */
    public function scopeSubcategories($query)
    {
        return $query->where('cc_level', self::LEVEL_SUBCATEGORY);
    }

    /**
     * Scope to filter by parent code.
     */
    public function scopeByParent($query, $parentCode)
    {
        return $query->where('cc_parent_code', $parentCode);
    }

    /**
     * Scope for active cost codes
     */
    public function scopeActive($query)
    {
        return $query->where('cc_status', 1);
    }

    /**
     * Scope for ordering by ID
     */
    public function scopeOrderById($query)
    {
        return $query->orderBy('cc_id', 'ASC');
    }

    /**
     * Format full code display.
     */
    public function getFormattedCode(): string
    {
        return $this->cc_full_code ?? $this->cc_no;
    }

    /**
     * Get hierarchical display name.
     */
    public function getHierarchicalName(): string
    {
        if ($this->cc_level == self::LEVEL_PARENT) {
            return "{$this->cc_parent_code} - {$this->cc_description}";
        } elseif ($this->cc_level == self::LEVEL_CATEGORY) {
            return "{$this->cc_parent_code}-{$this->cc_category_code} - {$this->cc_description}";
        } else {
            return "{$this->cc_full_code} - {$this->cc_description}";
        }
    }

    /**
     * Parse full code into components.
     */
    public static function parseFullCode($fullCode): array
    {
        $parts = explode('-', $fullCode);
        
        return [
            'parent' => $parts[0] ?? null,
            'category' => $parts[1] ?? null,
            'subcategory' => $parts[2] ?? null,
            'level' => count($parts),
        ];
    }

    /**
     * Build full code from components.
     */
    public static function buildFullCode($parent, $category = null, $subcategory = null): string
    {
        $code = str_pad($parent, 2, '0', STR_PAD_LEFT);
        
        if ($category) {
            $code .= '-' . str_pad($category, 2, '0', STR_PAD_LEFT);
        }
        
        if ($subcategory) {
            $code .= '-' . str_pad($subcategory, 2, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }
}
