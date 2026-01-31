<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'eq_master';
    protected $primaryKey = 'eq_id';
    public $timestamps = false;

    protected $fillable = [
        'eqm_asset_name',
        'eqm_asset_description',
        'eqm_asset_type',
        'eqm_asset_tag',
        'eqm_asset_picture',
        'eqm_asset_condition',
        'eqm_category',
        'eqm_status',
        'eqm_existing_reading',
        'eqm_estimate_usage',
        'eqm_remaining_life',
        'eqm_location',
        'eqm_supplier',
        'eqm_serial',
        'eqm_year',
        'eqm_license_plate',
        'eqm_current_operator',
        'eqm_purchase_price',
        'eqm_purchase_date',
        'eqm_current_value',
        'eqm_brand',
        'eqm_model',
        'eqm_war_expiry_date',
        'eqm_dep_method',
        'eqm_rental_total_value',
        'eqm_rental_insurance',
        'eqm_rental_insurance_amt',
        'eqm_created_date',
        'company_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
