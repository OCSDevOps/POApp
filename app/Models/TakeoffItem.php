<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TakeoffItem extends Model
{
    protected $table = 'takeoff_details';
    protected $primaryKey = 'tod_id';
    public $timestamps = false;

    protected $fillable = [
        'tod_takeoff_id',
        'tod_item_code',
        'tod_description',
        'tod_quantity',
        'tod_uom_id',
        'tod_unit_price',
        'tod_subtotal',
        'tod_cost_code_id',
        'tod_source',
        'tod_ai_confidence',
        'tod_notes',
        'tod_status',
        'tod_createdate',
        'company_id',
    ];

    protected $casts = [
        'tod_quantity' => 'decimal:4',
        'tod_unit_price' => 'decimal:2',
        'tod_subtotal' => 'decimal:2',
        'tod_ai_confidence' => 'decimal:2',
        'tod_status' => 'integer',
    ];

    public function takeoff()
    {
        return $this->belongsTo(Takeoff::class, 'tod_takeoff_id', 'to_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'tod_item_code', 'item_code');
    }

    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'tod_uom_id', 'uom_id');
    }

    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'tod_cost_code_id', 'cc_id');
    }

    public function scopeActive($query)
    {
        return $query->where('tod_status', 1);
    }

    public function scopeFromAi($query)
    {
        return $query->where('tod_source', 'ai');
    }

    public function scopeManual($query)
    {
        return $query->where('tod_source', 'manual');
    }

    public function getConfidenceBadgeAttribute(): string
    {
        if ($this->tod_source !== 'ai' || is_null($this->tod_ai_confidence)) {
            return '';
        }
        if ($this->tod_ai_confidence >= 80) return 'success';
        if ($this->tod_ai_confidence >= 50) return 'warning';
        return 'danger';
    }
}
