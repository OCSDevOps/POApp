<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyScope;

class ItemPackage extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'item_package_master';
    protected $primaryKey = 'ipack_id';
    public $timestamps = false;

    protected $fillable = [
        'ipack_name',
        'ipack_details',
        'ipack_totalitem',
        'ipack_total_qty',
        'ipack_createdate',
        'ipack_createby',
        'ipack_modifydate',
        'ipack_modifyby',
        'ipack_status',
    ];

    public function details()
    {
        return $this->hasMany(ItemPackageDetail::class, 'ipdetail_ipack_ms', 'ipack_id')->where('ipdetail_status', 1);
    }
}
