<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TakeoffDrawing extends Model
{
    protected $table = 'takeoff_drawings';
    protected $primaryKey = 'tdr_id';
    public $timestamps = false;

    protected $fillable = [
        'tdr_takeoff_id',
        'tdr_original_name',
        'tdr_path',
        'tdr_mime',
        'tdr_size',
        'tdr_page_count',
        'tdr_ai_status',
        'tdr_ai_processed_at',
        'tdr_ai_raw_response',
        'tdr_ai_error',
        'tdr_createby',
        'tdr_createdate',
        'tdr_status',
        'company_id',
    ];

    protected $casts = [
        'tdr_size' => 'integer',
        'tdr_page_count' => 'integer',
        'tdr_ai_processed_at' => 'datetime',
        'tdr_createdate' => 'datetime',
        'tdr_status' => 'integer',
    ];

    public function takeoff()
    {
        return $this->belongsTo(Takeoff::class, 'tdr_takeoff_id', 'to_id');
    }

    public function scopeActive($query)
    {
        return $query->where('tdr_status', 1);
    }

    public function getAiStatusBadgeAttribute(): string
    {
        return match ($this->tdr_ai_status) {
            'pending' => 'secondary',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->tdr_size;
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function isImage(): bool
    {
        return str_starts_with($this->tdr_mime, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->tdr_mime === 'application/pdf';
    }
}
