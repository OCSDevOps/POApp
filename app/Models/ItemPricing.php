<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPricing extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'item_pricing';
    protected $primaryKey = 'pricing_id';
    public $timestamps = true;

    protected $fillable = [
        'item_id',
        'supplier_id',
        'project_id',
        'company_id',
        'unit_price',
        'effective_from',
        'effective_to',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'status' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'sup_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'proj_id');
    }

    /**
     * Scope for active pricing records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now()->toDateString());
            })
            ->where('effective_from', '<=', now()->toDateString());
    }
}
