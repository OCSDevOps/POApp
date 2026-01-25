<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxGroup extends Model
{
    use HasFactory;

    protected $table = 'taxgroup_master';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'percentage',
        'description',
        'procore_tax_code_id',
        'created_at',
    ];
}
