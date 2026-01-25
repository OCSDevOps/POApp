<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcoreSyncLog extends Model
{
    use HasFactory;

    protected $table = 'procore_sync_log';
    protected $primaryKey = 'sync_id';
    public $timestamps = false;

    // Sync types
    const TYPE_PROJECTS = 'projects';
    const TYPE_COST_CODES = 'cost_codes';
    const TYPE_BUDGETS = 'budgets';
    const TYPE_COMMITMENTS = 'commitments';
    const TYPE_PURCHASE_ORDERS = 'purchase_orders';
    const TYPE_VENDORS = 'vendors';

    // Sync directions
    const DIRECTION_INBOUND = 'inbound';
    const DIRECTION_OUTBOUND = 'outbound';

    // Sync statuses
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';

    protected $fillable = [
        'sync_type',
        'sync_direction',
        'sync_entity_id',
        'sync_procore_id',
        'sync_status',
        'sync_message',
        'sync_request_data',
        'sync_response_data',
        'sync_created_at',
        'sync_created_by',
    ];

    protected $casts = [
        'sync_created_at' => 'datetime',
    ];

    /**
     * Get the user who initiated the sync.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'sync_created_by', 'id');
    }

    /**
     * Log a successful sync
     */
    public static function logSuccess($type, $direction, $entityId, $procoreId, $message = null, $requestData = null, $responseData = null)
    {
        return self::create([
            'sync_type' => $type,
            'sync_direction' => $direction,
            'sync_entity_id' => $entityId,
            'sync_procore_id' => $procoreId,
            'sync_status' => self::STATUS_SUCCESS,
            'sync_message' => $message,
            'sync_request_data' => is_array($requestData) ? json_encode($requestData) : $requestData,
            'sync_response_data' => is_array($responseData) ? json_encode($responseData) : $responseData,
            'sync_created_at' => now(),
            'sync_created_by' => auth()->id(),
        ]);
    }

    /**
     * Log a failed sync
     */
    public static function logFailure($type, $direction, $entityId, $procoreId, $message, $requestData = null, $responseData = null)
    {
        return self::create([
            'sync_type' => $type,
            'sync_direction' => $direction,
            'sync_entity_id' => $entityId,
            'sync_procore_id' => $procoreId,
            'sync_status' => self::STATUS_FAILED,
            'sync_message' => $message,
            'sync_request_data' => is_array($requestData) ? json_encode($requestData) : $requestData,
            'sync_response_data' => is_array($responseData) ? json_encode($responseData) : $responseData,
            'sync_created_at' => now(),
            'sync_created_by' => auth()->id(),
        ]);
    }

    /**
     * Scope for filtering by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('sync_type', $type);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('sync_status', $status);
    }

    /**
     * Scope for failed syncs
     */
    public function scopeFailed($query)
    {
        return $query->where('sync_status', self::STATUS_FAILED);
    }

    /**
     * Scope for recent syncs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('sync_created_at', '>=', now()->subDays($days));
    }

    /**
     * Get sync statistics
     */
    public static function getStatistics($days = 30)
    {
        $startDate = now()->subDays($days);
        
        return [
            'total' => self::where('sync_created_at', '>=', $startDate)->count(),
            'success' => self::where('sync_created_at', '>=', $startDate)->where('sync_status', self::STATUS_SUCCESS)->count(),
            'failed' => self::where('sync_created_at', '>=', $startDate)->where('sync_status', self::STATUS_FAILED)->count(),
            'by_type' => self::where('sync_created_at', '>=', $startDate)
                ->selectRaw('sync_type, COUNT(*) as count')
                ->groupBy('sync_type')
                ->pluck('count', 'sync_type')
                ->toArray(),
        ];
    }
}
