<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistPerformance extends Model
{
    protected $table = 'cl_perform_master';
    protected $primaryKey = 'cl_p_id';
    public $timestamps = false;

    protected $casts = [
        'cl_p_item_values' => 'array',
    ];

    protected $fillable = [
        'cl_id',
        'cl_eq_id',
        'cl_p_date',
        'cl_p_item_values',
        'status',
        'created_date',
        'modified_date',
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class, 'cl_id', 'cl_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'cl_eq_id', 'eq_id');
    }

    public function details()
    {
        return $this->hasMany(ChecklistPerformanceDetail::class, 'cl_p_id', 'cl_p_id')->where('status', 1);
    }
}
