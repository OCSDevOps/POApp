<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyScope;

class CostCodeTemplate extends Model
{
    use CompanyScope;

    protected $table = 'cost_code_templates';
    protected $primaryKey = 'cct_id';

    protected $fillable = [
        'company_id',
        'cct_key',
        'cct_name',
        'cct_description',
        'cct_status',
        'cct_createby',
        'cct_createdate',
        'cct_modifyby',
        'cct_modifydate',
    ];

    protected $casts = [
        'cct_createdate' => 'datetime',
        'cct_modifydate' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Template items (links to cost codes).
     */
    public function items()
    {
        return $this->hasMany(CostCodeTemplateItem::class, 'ccti_template_id', 'cct_id');
    }

    /**
     * Get the cost codes associated with this template (through items).
     */
    public function costCodes()
    {
        return $this->belongsToMany(
            CostCode::class,
            'cost_code_template_items',
            'ccti_template_id',
            'ccti_cost_code_id',
            'cct_id',
            'cc_id'
        );
    }

    /**
     * Creator.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'cct_createby', 'id');
    }

    /**
     * Scope: active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('cct_status', 1);
    }

    /**
     * Scope: order by name.
     */
    public function scopeOrderByName($query)
    {
        return $query->orderBy('cct_name');
    }
}
