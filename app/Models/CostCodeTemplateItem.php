<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCodeTemplateItem extends Model
{
    protected $table = 'cost_code_template_items';
    protected $primaryKey = 'ccti_id';
    public $timestamps = false;

    protected $fillable = [
        'ccti_template_id',
        'ccti_cost_code_id',
        'ccti_sort_order',
    ];

    /**
     * The template this item belongs to.
     */
    public function template()
    {
        return $this->belongsTo(CostCodeTemplate::class, 'ccti_template_id', 'cct_id');
    }

    /**
     * The cost code.
     */
    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'ccti_cost_code_id', 'cc_id');
    }
}
