<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPackageDetail extends Model
{
    use HasFactory;

    protected $table = 'item_package_details';
    protected $primaryKey = 'ipdetail_id';
    public $timestamps = false;

    protected $fillable = [
        'ipdetail_autogen',
        'ipdetail_ipack_ms',
        'ipdetail_item_ms',
        'ipdetail_quantity',
        'ipdetail_info',
        'ipdetail_createdate',
        'ipdetail_status',
    ];
}
