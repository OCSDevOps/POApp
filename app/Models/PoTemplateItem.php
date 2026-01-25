<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoTemplateItem extends Model
{
    use HasFactory;

    protected $table = 'po_template_items';
    protected $primaryKey = 'poti_id';
    public $timestamps = false;

    protected $fillable = [
        'poti_template_id',
        'poti_item_id',
        'poti_default_qty',
        'poti_uom_id',
        'poti_cost_code_id',
        'poti_notes',
        'poti_created_at',
    ];

    protected $casts = [
        'poti_created_at' => 'datetime',
    ];

    /**
     * Get the template for this item.
     */
    public function template()
    {
        return $this->belongsTo(PoTemplate::class, 'poti_template_id', 'pot_id');
    }

    /**
     * Get the item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'poti_item_id', 'item_id');
    }

    /**
     * Get the unit of measure.
     */
    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'poti_uom_id', 'uom_id');
    }

    /**
     * Get the cost code.
     */
    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'poti_cost_code_id', 'cc_id');
    }
}
