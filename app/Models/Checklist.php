<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $table = 'checklist_master';
    protected $primaryKey = 'cl_id';
    public $timestamps = false;

    protected $casts = [
        'cl_eq_ids' => 'array',
        'cl_user_ids' => 'array',
    ];

    protected $fillable = [
        'cl_name',
        'cl_frequency',
        'cl_eq_ids',
        'cl_user_ids',
        'cl_start_date',
        'status',
        'created_date',
        'modified_date',
    ];

    public function items()
    {
        return $this->hasMany(ChecklistItem::class, 'cl_id', 'cl_id')->where('status', 1);
    }

    public function performances()
    {
        return $this->hasMany(ChecklistPerformance::class, 'cl_id', 'cl_id')->where('status', 1);
    }
}
