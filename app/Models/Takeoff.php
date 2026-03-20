<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class Takeoff extends Model
{
    use CompanyScope;

    protected $table = 'takeoff_master';
    protected $primaryKey = 'to_id';
    public $timestamps = false;

    const STATUS_CANCELLED = 0;
    const STATUS_DRAFT = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_REVIEW = 3;
    const STATUS_FINALIZED = 4;

    const STATUS_LABELS = [
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_REVIEW => 'Review',
        self::STATUS_FINALIZED => 'Finalized',
    ];

    const STATUS_BADGES = [
        self::STATUS_CANCELLED => 'dark',
        self::STATUS_DRAFT => 'secondary',
        self::STATUS_PROCESSING => 'info',
        self::STATUS_REVIEW => 'warning',
        self::STATUS_FINALIZED => 'success',
    ];

    protected $fillable = [
        'to_number',
        'to_project_id',
        'to_title',
        'to_description',
        'to_status',
        'to_total_items',
        'to_subtotal',
        'to_tax_amount',
        'to_total_amount',
        'to_notes',
        'to_finalized_by',
        'to_finalized_at',
        'to_createby',
        'to_createdate',
        'to_modifyby',
        'to_modifydate',
        'company_id',
    ];

    protected $casts = [
        'to_status' => 'integer',
        'to_total_items' => 'integer',
        'to_subtotal' => 'decimal:2',
        'to_tax_amount' => 'decimal:2',
        'to_total_amount' => 'decimal:2',
        'to_finalized_at' => 'datetime',
        'to_createdate' => 'datetime',
        'to_modifydate' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'to_project_id', 'proj_id');
    }

    public function items()
    {
        return $this->hasMany(TakeoffItem::class, 'tod_takeoff_id', 'to_id');
    }

    public function activeItems()
    {
        return $this->hasMany(TakeoffItem::class, 'tod_takeoff_id', 'to_id')->where('tod_status', 1);
    }

    public function drawings()
    {
        return $this->hasMany(TakeoffDrawing::class, 'tdr_takeoff_id', 'to_id');
    }

    public function activeDrawings()
    {
        return $this->hasMany(TakeoffDrawing::class, 'tdr_takeoff_id', 'to_id')->where('tdr_status', 1);
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'to_finalized_by', 'id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->to_status] ?? 'Unknown';
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_BADGES[$this->to_status] ?? 'secondary';
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('to_project_id', $projectId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('to_status', $status);
    }

    public function recalculateTotals(): void
    {
        $items = $this->activeItems;
        $this->to_total_items = $items->count();
        $this->to_subtotal = $items->sum('tod_subtotal');
        $this->to_total_amount = $this->to_subtotal + $this->to_tax_amount;
        $this->save();
    }

    public static function generateNumber(): string
    {
        $last = self::withoutGlobalScopes()->max('to_id') ?? 0;
        return 'TO-' . str_pad($last + 1, 6, '0', STR_PAD_LEFT);
    }
}
