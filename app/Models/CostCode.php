<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCode extends Model
{
    use HasFactory;

    protected $table = 'cost_code_master';
    protected $primaryKey = 'cc_id';
    public $timestamps = false;

    protected $fillable = [
        'cc_no',
        'cc_name',
        'cc_description',
        'cc_status',
        'cc_created_by',
        'cc_created_at',
        'cc_modifyby',
        'cc_modifydate',
    ];

    /**
     * Get the items for the cost code.
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'item_ccode_ms', 'cc_id');
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
}
