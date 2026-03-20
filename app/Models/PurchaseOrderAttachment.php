<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderAttachment extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'purchase_order_attachments';
    protected $primaryKey = 'po_attachment_id';
    public $timestamps = false;

    protected $fillable = [
        'po_attachment_porder_ms',
        'po_attachment_original_name',
        'po_attachment_path',
        'po_attachment_mime',
        'po_attachment_size',
        'po_attachment_createdate',
        'po_attachment_createby',
        'po_attachment_status',
        'company_id',
    ];

    protected $casts = [
        'po_attachment_createdate' => 'datetime',
        'po_attachment_size' => 'integer',
        'po_attachment_status' => 'integer',
    ];

    /**
     * Get the purchase order that owns this attachment.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_attachment_porder_ms', 'porder_id');
    }

    /**
     * Scope active attachments.
     */
    public function scopeActive($query)
    {
        return $query->where('po_attachment_status', 1);
    }
}
