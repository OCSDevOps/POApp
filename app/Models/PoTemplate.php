<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoTemplate extends Model
{
    use HasFactory;

    protected $table = 'po_template_master';
    protected $primaryKey = 'pot_id';
    public $timestamps = false;

    protected $fillable = [
        'pot_name',
        'pot_description',
        'pot_supplier_id',
        'pot_project_id',
        'pot_terms',
        'pot_delivery_notes',
        'pot_is_active',
        'pot_created_by',
        'pot_created_at',
        'pot_modified_by',
        'pot_modified_at',
    ];

    protected $casts = [
        'pot_is_active' => 'boolean',
        'pot_created_at' => 'datetime',
        'pot_modified_at' => 'datetime',
    ];

    /**
     * Get the supplier for this template.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'pot_supplier_id', 'sup_id');
    }

    /**
     * Get the project for this template.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'pot_project_id', 'proj_id');
    }

    /**
     * Get the items for this template.
     */
    public function items()
    {
        return $this->hasMany(PoTemplateItem::class, 'poti_template_id', 'pot_id');
    }

    /**
     * Get the user who created this template.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'pot_created_by', 'id');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('pot_is_active', true);
    }

    /**
     * Scope for filtering by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('pot_supplier_id', $supplierId);
    }

    /**
     * Scope for filtering by project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('pot_project_id', $projectId);
    }

    /**
     * Create a Purchase Order from this template
     */
    public function createPurchaseOrder($projectId = null, $supplierId = null, $quantities = [])
    {
        $projectId = $projectId ?? $this->pot_project_id;
        $supplierId = $supplierId ?? $this->pot_supplier_id;

        if (!$projectId || !$supplierId) {
            throw new \Exception('Project and Supplier are required');
        }

        // Get project address
        $project = Project::find($projectId);
        
        // Generate PO number
        $poNumber = PurchaseOrder::generatePoNumber();

        // Create PO
        $po = PurchaseOrder::create([
            'porder_no' => $poNumber,
            'porder_project_ms' => $projectId,
            'porder_supplier_ms' => $supplierId,
            'porder_address' => $project->proj_address ?? '',
            'porder_delivery_note' => $this->pot_delivery_notes,
            'porder_description' => "Created from template: {$this->pot_name}",
            'porder_total_item' => 0,
            'porder_total_amount' => 0,
            'porder_delivery_status' => 0,
            'porder_createdate' => now(),
            'porder_createby' => auth()->id() ?? 1,
            'porder_status' => 1,
        ]);

        // Add items from template
        $totalAmount = 0;
        $totalItems = 0;

        foreach ($this->items as $templateItem) {
            $quantity = $quantities[$templateItem->poti_item_id] ?? $templateItem->poti_default_qty;
            
            // Get current price from supplier catalog
            $catalogItem = SupplierCatalog::where('supcat_supplier', $supplierId)
                ->where('supcat_item_code', $templateItem->item->item_code)
                ->where('supcat_status', 1)
                ->first();

            if ($catalogItem) {
                $unitPrice = $catalogItem->supcat_price;
                $subtotal = $quantity * $unitPrice;
                $totalAmount += $subtotal;
                $totalItems++;

                // Create PO detail
                \DB::table('purchase_order_details')->insert([
                    'po_detail_autogen' => date('dmyHis'),
                    'po_detail_porder_ms' => $po->porder_id,
                    'po_detail_item' => $templateItem->item->item_code,
                    'po_detail_sku' => $catalogItem->supcat_sku_no,
                    'po_detail_taxcode' => '0',
                    'po_detail_quantity' => $quantity,
                    'po_detail_unitprice' => $unitPrice,
                    'po_detail_subtotal' => $subtotal,
                    'po_detail_taxamount' => 0,
                    'po_detail_total' => $subtotal,
                    'po_detail_createdate' => now(),
                    'po_detail_status' => 1,
                ]);
            }
        }

        // Update PO totals
        $po->update([
            'porder_total_item' => $totalItems,
            'porder_total_amount' => $totalAmount,
        ]);

        return $po;
    }
}
