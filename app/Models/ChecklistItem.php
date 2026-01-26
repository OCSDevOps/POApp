<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $table = 'checklist_details';
    protected $primaryKey = 'cli_id';
    public $timestamps = false;

    protected $fillable = [
        'cl_id',
        'cli_item',
        'status',
        'created_date',
        'modified_date',
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class, 'cl_id', 'cl_id');
    }
}
