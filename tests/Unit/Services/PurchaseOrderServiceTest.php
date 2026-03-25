<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PurchaseOrderService;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\CostCode;
use App\Models\Company;
use App\Models\User;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\RfqSupplier;
use App\Models\RfqQuote;
use App\Models\SupplierCatalog;
use App\Models\UnitOfMeasure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;

class PurchaseOrderServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected PurchaseOrderService $poService;
    protected Company $company;
    protected User $user;
    protected Project $project;
    protected Supplier $supplier;
    protected CostCode $costCode;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->poService = new PurchaseOrderService();
        Notification::fake();
        
        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'subdomain' => 'test',
            'status' => 1,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $this->project = Project::create([
            'proj_name' => 'Test Project',
            'proj_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->supplier = Supplier::create([
            'sup_name' => 'Test Supplier',
            'sup_email' => 'supplier@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->costCode = CostCode::create([
            'cc_no' => '01',
            'cc_description' => 'Test Cost Code',
            'cc_level' => 1,
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->item = Item::create([
            'item_code' => 'ITEM001',
            'item_name' => 'Test Item',
            'item_ccode_ms' => $this->costCode->cc_id,
            'item_status' => 1,
            'company_id' => $this->company->id,
        ]);

        // Set company context and authenticate
        session(['company_id' => $this->company->id]);
        $this->actingAs($this->user);
    }

    // ==========================================
    // PO Number Generation Tests
    // ==========================================

    /** @test */
    public function it_generates_po_number()
    {
        $poNumber = $this->poService->generatePoNumber();
        
        $this->assertStringStartsWith('PO', $poNumber);
        $this->assertEquals(8, strlen($poNumber)); // PO + 6 digits
    }

    /** @test */
    public function it_generates_sequential_po_numbers()
    {
        // Create an existing PO
        PurchaseOrder::create([
            'porder_no' => 'PO000010',
            'porder_createdate' => now(),
            'porder_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $poNumber = $this->poService->generatePoNumber();
        
        $this->assertEquals('PO000011', $poNumber);
    }

    // ==========================================
    // Create Purchase Order Tests
    // ==========================================

    /** @test */
    public function it_creates_purchase_order_with_items()
    {
        config(['app.budget_constraints_enabled' => false]);

        $items = [
            [
                'item_code' => 'ITEM001',
                'sku' => 'SKU001',
                'quantity' => 10,
                'unit_price' => 100.00,
                'tax_rate' => 10,
                'tax_group' => 'GST',
            ],
            [
                'item_code' => 'ITEM002',
                'sku' => 'SKU002',
                'quantity' => 5,
                'unit_price' => 200.00,
                'tax_rate' => 10,
            ],
        ];

        $po = $this->poService->createPurchaseOrder([
            'project_id' => $this->project->proj_id,
            'supplier_id' => $this->supplier->sup_id,
            'address' => '123 Test Street',
            'delivery_note' => 'Test delivery note',
            'description' => 'Test PO description',
        ], $items);

        $this->assertDatabaseHas('purchase_order_master', [
            'porder_id' => $po->porder_id,
            'porder_project_ms' => $this->project->proj_id,
            'porder_supplier_ms' => $this->supplier->sup_id,
            'porder_address' => '123 Test Street',
            'porder_total_item' => 2,
            'porder_status' => 1,
        ]);

        $this->assertEquals(2, $po->porder_total_item);
        $this->assertEquals(2200.00, $po->porder_total_amount); // Two $1000 lines plus 10% tax each

        // Check items were created
        $this->assertDatabaseHas('purchase_order_detail', [
            'po_detail_porder_ms' => $po->porder_id,
            'po_detail_item' => 'ITEM001',
            'po_detail_quantity' => 10,
            'po_detail_unitprice' => 100.00,
        ]);
    }

    /** @test */
    public function it_creates_po_with_zero_items()
    {
        config(['app.budget_constraints_enabled' => false]);

        $po = $this->poService->createPurchaseOrder([
            'project_id' => $this->project->proj_id,
            'supplier_id' => $this->supplier->sup_id,
        ], []);

        $this->assertEquals(0, $po->porder_total_item);
        $this->assertEquals(0, $po->porder_total_amount);
    }

    // ==========================================
    // RFQ Management Tests
    // ==========================================

    /** @test */
    public function it_creates_rfq_with_items_and_suppliers()
    {
        $supplier2 = Supplier::create([
            'sup_name' => 'Second Supplier',
            'sup_email' => 'supplier2@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $uom = UnitOfMeasure::create([
            'uom_code' => 'EA',
            'uom_name' => 'Each',
            'company_id' => $this->company->id,
        ]);

        $items = [
            [
                'item_id' => $this->item->item_id,
                'quantity' => 100,
                'uom_id' => $uom->uom_id,
                'target_price' => 90.00,
                'notes' => 'Need by next week',
            ],
        ];

        $rfq = $this->poService->createRfq([
            'project_id' => $this->project->proj_id,
            'title' => 'Test RFQ',
            'description' => 'Test RFQ description',
            'due_date' => now()->addDays(7),
        ], $items, [$this->supplier->sup_id, $supplier2->sup_id]);

        $this->assertDatabaseHas('rfqs', [
            'rfq_id' => $rfq->rfq_id,
            'rfq_title' => 'Test RFQ',
            'rfq_project_id' => $this->project->proj_id,
            'rfq_status' => Rfq::STATUS_DRAFT,
        ]);

        $this->assertDatabaseHas('rfq_items', [
            'rfqi_rfq_id' => $rfq->rfq_id,
            'rfqi_item_id' => $this->item->item_id,
            'rfqi_quantity' => 100,
        ]);

        $this->assertDatabaseHas('rfq_suppliers', [
            'rfqs_rfq_id' => $rfq->rfq_id,
            'rfqs_supplier_id' => $this->supplier->sup_id,
        ]);

        $this->assertEquals(2, $rfq->suppliers()->count());
    }

    /** @test */
    public function it_records_supplier_quote()
    {
        $rfq = Rfq::create([
            'rfq_no' => 'RFQ-001',
            'rfq_project_id' => $this->project->proj_id,
            'rfq_title' => 'Test RFQ',
            'rfq_status' => Rfq::STATUS_SENT,
            'rfq_created_by' => $this->user->id,
            'rfq_created_at' => now(),
        ]);

        $rfqItem = RfqItem::create([
            'rfqi_rfq_id' => $rfq->rfq_id,
            'rfqi_item_id' => $this->item->item_id,
            'rfqi_quantity' => 100,
            'rfqi_uom_id' => 1,
            'rfqi_created_at' => now(),
        ]);

        $rfqSupplier = RfqSupplier::create([
            'rfqs_rfq_id' => $rfq->rfq_id,
            'rfqs_supplier_id' => $this->supplier->sup_id,
            'rfqs_status' => RfqSupplier::STATUS_SENT,
            'rfqs_created_at' => now(),
        ]);

        $quotes = [
            [
                'rfq_item_id' => $rfqItem->rfqi_id,
                'price' => 85.00,
                'lead_time_days' => 5,
                'valid_until' => now()->addDays(30),
                'notes' => 'Volume discount applied',
            ],
        ];

        $result = $this->poService->recordQuote($rfqSupplier->rfqs_id, $quotes);

        $this->assertDatabaseHas('rfq_quotes', [
            'rfqq_rfqs_id' => $rfqSupplier->rfqs_id,
            'rfqq_rfqi_id' => $rfqItem->rfqi_id,
            'rfqq_quoted_price' => 85.00,
            'rfqq_lead_time_days' => 5,
        ]);

        $this->assertEquals(RfqSupplier::STATUS_RESPONDED, $result->fresh()->rfqs_status);
    }

    /** @test */
    public function it_converts_rfq_to_purchase_order()
    {
        config(['app.budget_constraints_enabled' => false]);

        // Create supplier catalog entry
        SupplierCatalog::create([
            'supcat_supplier' => $this->supplier->sup_id,
            'supcat_item_code' => $this->item->item_code,
            'supcat_sku_no' => 'SUPP-SKU-001',
            'supcat_price' => 90.00,
            'supcat_createdate' => now(),
            'supcat_status' => 1,
        ]);

        $rfq = Rfq::create([
            'rfq_no' => 'RFQ-001',
            'rfq_project_id' => $this->project->proj_id,
            'rfq_title' => 'Test RFQ',
            'rfq_status' => Rfq::STATUS_SENT,
            'rfq_created_by' => $this->user->id,
            'rfq_created_at' => now(),
        ]);

        $rfqItem = RfqItem::create([
            'rfqi_rfq_id' => $rfq->rfq_id,
            'rfqi_item_id' => $this->item->item_id,
            'rfqi_quantity' => 100,
            'rfqi_uom_id' => 1,
            'rfqi_created_at' => now(),
        ]);

        $rfqSupplier = RfqSupplier::create([
            'rfqs_rfq_id' => $rfq->rfq_id,
            'rfqs_supplier_id' => $this->supplier->sup_id,
            'rfqs_status' => RfqSupplier::STATUS_RESPONDED,
            'rfqs_created_at' => now(),
        ]);

        RfqQuote::create([
            'rfqq_rfqs_id' => $rfqSupplier->rfqs_id,
            'rfqq_rfqi_id' => $rfqItem->rfqi_id,
            'rfqq_quoted_price' => 85.00,
            'rfqq_created_at' => now(),
        ]);

        $po = $this->poService->convertRfqToPo($rfq->rfq_id, $this->supplier->sup_id);

        $this->assertNotNull($po);
        $this->assertEquals($this->project->proj_id, $po->porder_project_ms);
        $this->assertEquals($this->supplier->sup_id, $po->porder_supplier_ms);
        
        // Check RFQ status updated
        $this->assertEquals(Rfq::STATUS_CONVERTED, $rfq->fresh()->rfq_status);

        // Check PO items created with quoted price
        $this->assertDatabaseHas('purchase_order_detail', [
            'po_detail_porder_ms' => $po->porder_id,
            'po_detail_item' => $this->item->item_code,
            'po_detail_quantity' => 100,
            'po_detail_unitprice' => 85.00,
        ]);
    }

    // ==========================================
    // Supplier Portal Tests
    // ==========================================

    /** @test */
    public function it_gets_supplier_dashboard_data()
    {
        // Create POs for supplier
        PurchaseOrder::create([
            'porder_no' => 'PO001',
            'porder_project_ms' => $this->project->proj_id,
            'porder_supplier_ms' => $this->supplier->sup_id,
            'porder_total_amount' => 5000.00,
            'porder_status' => 1,
            'porder_delivery_status' => 0,
            'porder_createdate' => now(),
            'company_id' => $this->company->id,
        ]);

        PurchaseOrder::create([
            'porder_no' => 'PO002',
            'porder_project_ms' => $this->project->proj_id,
            'porder_supplier_ms' => $this->supplier->sup_id,
            'porder_total_amount' => 3000.00,
            'porder_status' => 1,
            'porder_delivery_status' => 1, // Fully received
            'porder_createdate' => now(),
            'company_id' => $this->company->id,
        ]);

        // Add catalog items
        SupplierCatalog::create([
            'supcat_supplier' => $this->supplier->sup_id,
            'supcat_item_code' => 'ITEM001',
            'supcat_price' => 100.00,
            'supcat_status' => 1,
        ]);

        $dashboard = $this->poService->getSupplierDashboard($this->supplier->sup_id);

        $this->assertEquals(2, $dashboard['total_orders']);
        $this->assertEquals(1, $dashboard['pending_orders']); // One not received
        $this->assertEquals(8000.00, $dashboard['total_order_value']);
        $this->assertEquals(1, $dashboard['catalog_items']);
    }

    /** @test */
    public function it_gets_supplier_catalog()
    {
        SupplierCatalog::create([
            'supcat_supplier' => $this->supplier->sup_id,
            'supcat_item_code' => $this->item->item_code,
            'supcat_sku_no' => 'SUP-001',
            'supcat_price' => 95.00,
            'supcat_status' => 1,
        ]);

        $catalog = $this->poService->getSupplierCatalog($this->supplier->sup_id);

        $this->assertCount(1, $catalog);
        $this->assertEquals('SUP-001', $catalog->first()->supcat_sku_no);
        $this->assertEquals(95.00, $catalog->first()->supcat_price);
    }

    /** @test */
    public function it_adds_item_to_supplier_catalog()
    {
        $uom = UnitOfMeasure::create([
            'uom_code' => 'EA',
            'uom_name' => 'Each',
            'company_id' => $this->company->id,
        ]);

        $catalogItem = $this->poService->addToCatalog($this->supplier->sup_id, [
            'item_code' => $this->item->item_code,
            'sku_no' => 'SUP-NEW-001',
            'uom_id' => $uom->uom_id,
            'price' => 120.00,
            'details' => 'New catalog item',
        ]);

        $this->assertDatabaseHas('supplier_catalog', [
            'supcat_supplier' => $this->supplier->sup_id,
            'supcat_item_code' => $this->item->item_code,
            'supcat_sku_no' => 'SUP-NEW-001',
            'supcat_price' => 120.00,
        ]);
    }

    // ==========================================
    // Price Tracking Tests
    // ==========================================

    /** @test */
    public function it_tracks_item_price_changes()
    {
        SupplierCatalog::create([
            'supcat_supplier' => $this->supplier->sup_id,
            'supcat_item_code' => $this->item->item_code,
            'supcat_price' => 90.00,
            'supcat_lastdate' => now()->subDays(30),
            'supcat_createdate' => now()->subDays(30),
            'supcat_status' => 1,
        ]);

        $this->poService->updateItemPrice(
            $this->item->item_id,
            $this->supplier->sup_id,
            90.00,
            100.00,
            now()->toDateString(),
            'Price increase due to market conditions'
        );

        $this->assertDatabaseHas('item_price_history', [
            'iph_item_id' => $this->item->item_id,
            'iph_supplier_id' => $this->supplier->sup_id,
            'iph_old_price' => 90.00,
            'iph_new_price' => 100.00,
        ]);

        $this->assertDatabaseHas('supplier_catalog', [
            'supcat_supplier' => $this->supplier->sup_id,
            'supcat_item_code' => $this->item->item_code,
            'supcat_price' => 100.00,
        ]);
    }

    /** @test */
    public function it_gets_price_history_for_item()
    {
        SupplierCatalog::create([
            'supcat_supplier' => $this->supplier->sup_id,
            'supcat_item_code' => $this->item->item_code,
            'supcat_price' => 100.00,
            'supcat_status' => 1,
        ]);

        // Record multiple price changes
        $this->poService->updateItemPrice($this->item->item_id, $this->supplier->sup_id, 80.00, 90.00);
        $this->poService->updateItemPrice($this->item->item_id, $this->supplier->sup_id, 90.00, 100.00);

        $history = $this->poService->getItemPriceHistory($this->item->item_id);

        $this->assertCount(2, $history);
        $this->assertEquals(100.00, $history->first()->iph_new_price);
    }

    /** @test */
    public function it_gets_price_comparison_across_suppliers()
    {
        $supplier2 = Supplier::create([
            'sup_name' => 'Second Supplier',
            'sup_email' => 'supplier2@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        SupplierCatalog::create([
            'supcat_supplier' => $this->supplier->sup_id,
            'supcat_item_code' => $this->item->item_code,
            'supcat_price' => 100.00,
            'supcat_status' => 1,
        ]);

        SupplierCatalog::create([
            'supcat_supplier' => $supplier2->sup_id,
            'supcat_item_code' => $this->item->item_code,
            'supcat_price' => 90.00,
            'supcat_status' => 1,
        ]);

        $comparison = $this->poService->getPriceComparison($this->item->item_code);

        $this->assertCount(2, $comparison);
        $this->assertEquals(90.00, $comparison->first()->supcat_price); // Lowest first
    }
}
