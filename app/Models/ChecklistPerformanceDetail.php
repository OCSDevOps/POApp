<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistPerformanceDetail extends Model
{
    protected $table = 'cl_perform_details';
    protected $primaryKey = 'cl_pd_id';
    public $timestamps = false;

    protected $fillable = [
        'cl_p_id',
        'cl_pd_cli_id',
        'cl_pd_cli_value',
        'cl_pd_cli_notes',
        'cl_pd_cli_attachment',
        'status',
        'created_date',
        'modified_date',
    ];

    public function performance()
    {
        return $this->belongsTo(ChecklistPerformance::class, 'cl_p_id', 'cl_p_id');
    }

    public function item()
    {
        return $this->belongsTo(ChecklistItem::class, 'cl_pd_cli_id', 'cli_id');
    }
}
