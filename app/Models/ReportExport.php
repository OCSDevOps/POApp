<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    use HasFactory;

    protected $table = 'report_exports';
    protected $primaryKey = 'report_export_id';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'user_id',
        'report_type',
        'export_format',
        'status',
        'parameters',
        'file_name',
        'file_path',
        'error_message',
        'queued_at',
        'started_at',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Requesting user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Decode parameters JSON.
     */
    public function getParametersDecodedAttribute(): array
    {
        return json_decode($this->parameters ?? '[]', true) ?: [];
    }
}
