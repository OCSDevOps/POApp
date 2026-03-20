<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDetail extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'project_details';
    protected $primaryKey = 'pdetail_id';
    public $timestamps = false;

    protected $fillable = [
        'pdetail_proj_ms',
        'pdetail_user',
        'pdetail_info',
        'pdetail_createdate',
        'pdetail_status',
        'company_id',
    ];

    protected $casts = [
        'pdetail_createdate' => 'datetime',
    ];

    /**
     * Get the project that owns the detail.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'pdetail_proj_ms', 'proj_id');
    }

    /**
     * Get the user that created the detail.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'pdetail_user', 'u_id');
    }

    /**
     * Scope for active details
     */
    public function scopeActive($query)
    {
        return $query->where('pdetail_status', 1);
    }
}
