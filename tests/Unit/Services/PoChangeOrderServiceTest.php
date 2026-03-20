<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PoChangeOrderService;
use App\Services\BudgetService;
use App\Models\PoChangeOrder;
use App\Models\PurchaseOrder;
use App\Models\Budget;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\Company;
use App\Models\User;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class PoChangeOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PoChangeOrderService $poChangeOrderService;
    protected Company $company;
    protected User $user;
    protected User $approver;
    protected Project $project;
    protected Supplier $supplier;
    protected CostCode $costCode;
    protected Item $item;
    protected PurchaseOrder $purchaseOrder;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->poChangeOrderService = new PoChangeOrderService();
        Queue::fake();
        
        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'subdomain' => 'test',
            'status' => 1,
        ]);

        $this->user = User::create([
            'name' => 'Requester',
            'email' => 'requester@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $this->approver = User::create([
            'name' => 'Approver',
            'email' => 'approver@test.com',
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

        $this->purchaseOrder = PurchaseOrder::create([
            'porder_no' => 'PO000001',
            'porder_project_ms' => $this->project->proj_id,
            'porder_supplier_ms' => $this->supplier->sup_id,
            'porder_total_amount' => 10000.00,
            'porder_status' => 1,
            'porder_createdate' => now(),
            'company_id' => $this->company->id,
        ]);

        // Set company context and authenticate
        session(['company_id' => $this->company->id]);
        $this->actingAs($this->user);
    }

    // ==========================================
    // Create PO Change Order Tests
    // ==========================================

    /** @test */
    public function it_creates_po_change_order_with_amount_increase()
    {
        $result = $this->poChangeOrderService->createPoChangeOrder([
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 2000.00,
            'poco_description' => 'Additional items needed',
            'poco_notes' => 'Scope increased',
            'poco_reference' => 'REF-001',
            'created_by' => $this->user->id,
        ]);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['poco']);
        $this->assertEquals(2000.00, $result['poco']->poco_amount);
        $this->assertEquals(10000.00, $result['poco']->previous_total);
        $this->assertEquals(12000.00, $result['poco']->new_total);
        $this->assertEquals('draft', $result['poco']->poco_status);
        $this->assertEquals('amount_change', $result['poco']->poco_type);
    }

    /** @test */
    public function it_creates_po_change_order_with_amount_decrease()
    {
        $result = $this->poChangeOrderService->createPoChangeOrder([
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => -1500.00,
            'poco_description' => 'Reduced quantity',
            'created_by' => $this->user->id,
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals(-1500.00, $result['poco']->poco_amount);
        $this->assertEquals(10000.00, $result['poco']->previous_total);
        $this->assertEquals(8500.00, $result['poco']->new_total);
    }

    /** @test */
    public function it_fails_to_create_poco_for_nonexistent_po()
    {
        $result = $this->poChangeOrderService->createPoChangeOrder([
            'purchase_order_id' => 99999,
            'poco_type' => 'amount_change',
            'poco_amount' => 2000.00,
            'poco_description' => 'Test',
            'created_by' => $this->user->id,
        ]);

        $this->assertFalse($result['success']);
    }

    /** @test */
    public function it_creates_poco_with_details_array()
    {
        $details = [
            'line_items' => [
                ['item' => 'Item 1', 'qty' => 5, 'price' => 100],
                ['item' => 'Item 2', 'qty' => 3, 'price' => 200],
            ],
            'justification' => 'Project requirements changed',
        ];

        $result = $this->poChangeOrderService->createPoChangeOrder([
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 1100.00,
            'poco_description' => 'Additional line items',
            'poco_details' => $details,
            'created_by' => $this->user->id,
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals($details, $result['poco']->poco_details);
    }

    // ==========================================
    // Approve PO Change Order Tests
    // ==========================================

    /** @test */
    public function it_approves_poco_and_updates_po_total()
    {
        $poco = PoChangeOrder::create([
            'company_id' => $this->company->id,
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 2000.00,
            'previous_total' => 10000.00,
            'new_total' => 12000.00,
            'poco_description' => 'Test change order',
            'poco_status' => 'pending_approval',
            'created_by' => $this->user->id,
        ]);

        $result = $this->poChangeOrderService->approvePoChangeOrder(
            $poco->poco_id,
            $this->approver->id
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('approved', $result['poco']->fresh()->poco_status);
        $this->assertEquals(12000.00, $result['po']->fresh()->porder_total_amount);
        $this->assertEquals(2000.00, $result['po']->fresh()->porder_change_orders_total);
        $this->assertNotNull($result['poco']->approved_at);
        $this->assertEquals($this->approver->id, $result['poco']->approved_by);
    }

    /** @test */
    public function it_stores_original_total_on_first_approval()
    {
        $poco = PoChangeOrder::create([
            'company_id' => $this->company->id,
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 2000.00,
            'previous_total' => 10000.00,
            'new_total' => 12000.00,
            'poco_description' => 'Test change order',
            'poco_status' => 'pending_approval',
            'created_by' => $this->user->id,
        ]);

        $this->poChangeOrderService->approvePoChangeOrder($poco->poco_id, $this->approver->id);

        $this->assertEquals(10000.00, $this->purchaseOrder->fresh()->porder_original_total);
    }

    /** @test */
    public function it_cannot_approve_poco_in_invalid_status()
    {
        $poco = PoChangeOrder::create([
            'company_id' => $this->company->id,
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 2000.00,
            'previous_total' => 10000.00,
            'new_total' => 12000.00,
            'poco_description' => 'Test change order',
            'poco_status' => 'approved', // Already approved
            'created_by' => $this->user->id,
            'approved_by' => $this->approver->id,
            'approved_at' => now(),
        ]);

        $result = $this->poChangeOrderService->approvePoChangeOrder(
            $poco->poco_id,
            $this->approver->id
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('cannot be approved', $result['error']);
    }

    /** @test */
    public function it_handles_zero_amount_change_order()
    {
        $poco = PoChangeOrder::create([
            'company_id' => $this->company->id,
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'term_change',
            'poco_amount' => 0,
            'previous_total' => 10000.00,
            'new_total' => 10000.00,
            'poco_description' => 'Terms change only',
            'poco_status' => 'pending_approval',
            'created_by' => $this->user->id,
        ]);

        $result = $this->poChangeOrderService->approvePoChangeOrder(
            $poco->poco_id,
            $this->approver->id
        );

        $this->assertTrue($result['success']);
        // PO total should remain unchanged
        $this->assertEquals(10000.00, $result['po']->fresh()->porder_total_amount);
    }

    // ==========================================
    // Get PO Change Order History Tests
    // ==========================================

    /** @test */
    public function it_gets_po_change_order_history()
    {
        // Create approved PCO
        PoChangeOrder::create([
            'company_id' => $this->company->id,
            'poco_number' => 'PCO-2024-0001',
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 2000.00,
            'previous_total' => 10000.00,
            'new_total' => 12000.00,
            'poco_description' => 'First change',
            'poco_status' => 'approved',
            'created_by' => $this->user->id,
            'approved_by' => $this->approver->id,
            'approved_at' => now(),
        ]);

        // Create pending PCO
        PoChangeOrder::create([
            'company_id' => $this->company->id,
            'poco_number' => 'PCO-2024-0002',
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 1000.00,
            'previous_total' => 12000.00,
            'new_total' => 13000.00,
            'poco_description' => 'Second change',
            'poco_status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        // Create rejected PCO
        PoChangeOrder::create([
            'company_id' => $this->company->id,
            'poco_number' => 'PCO-2024-0003',
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 5000.00,
            'previous_total' => 10000.00,
            'new_total' => 15000.00,
            'poco_description' => 'Rejected change',
            'poco_status' => 'rejected',
            'created_by' => $this->user->id,
        ]);

        $result = $this->poChangeOrderService->getPoChangeOrderHistory($this->purchaseOrder->porder_id);

        $this->assertCount(3, $result['change_orders']);
        $this->assertEquals(2000.00, $result['total_changes']); // Only approved
        $this->assertEquals(1, $result['pending_count']); // Draft counts as pending
    }

    /** @test */
    public function it_returns_empty_history_for_po_without_change_orders()
    {
        $result = $this->poChangeOrderService->getPoChangeOrderHistory($this->purchaseOrder->porder_id);

        $this->assertCount(0, $result['change_orders']);
        $this->assertEquals(0, $result['total_changes']);
        $this->assertEquals(0, $result['pending_count']);
    }

    // ==========================================
    // Validate PO Change Order Tests
    // ==========================================

    /** @test */
    public function it_validates_poco_increase_against_budget()
    {
        // Create budget for the project
        Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 20000.00,
            'budget_revised_amount' => 20000.00,
            'budget_committed_amount' => 10000.00, // PO already committed
            'budget_spent_amount' => 0,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $result = $this->poChangeOrderService->validatePoChangeOrder(
            $this->purchaseOrder->porder_id,
            5000.00 // Increase amount
        );

        $this->assertTrue($result['valid']);
        $this->assertEquals(10000.00, $result['current_total']);
        $this->assertEquals(5000.00, $result['change_amount']);
        $this->assertEquals(15000.00, $result['new_total']);
    }

    /** @test */
    public function it_rejects_poco_when_budget_insufficient()
    {
        // Create budget with limited remaining
        Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 12000.00,
            'budget_revised_amount' => 12000.00,
            'budget_committed_amount' => 10000.00, // Only 2000 remaining
            'budget_spent_amount' => 0,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $result = $this->poChangeOrderService->validatePoChangeOrder(
            $this->purchaseOrder->porder_id,
            5000.00 // Try to increase by 5000
        );

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Insufficient budget', $result['reason']);
    }

    /** @test */
    public function it_validates_poco_decrease_without_budget_check()
    {
        // No budget needed for decreases
        $result = $this->poChangeOrderService->validatePoChangeOrder(
            $this->purchaseOrder->porder_id,
            -3000.00 // Decrease
        );

        $this->assertTrue($result['valid']);
        $this->assertEquals(-3000.00, $result['change_amount']);
        $this->assertEquals(7000.00, $result['new_total']);
    }

    /** @test */
    public function it_fails_validation_for_nonexistent_po()
    {
        $result = $this->poChangeOrderService->validatePoChangeOrder(
            99999,
            2000.00
        );

        $this->assertFalse($result['valid']);
    }

    // ==========================================
    // Integration Tests
    // ==========================================

    /** @test */
    public function it_creates_and_approves_poco_full_workflow()
    {
        // Step 1: Create PCO
        $createResult = $this->poChangeOrderService->createPoChangeOrder([
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 3000.00,
            'poco_description' => 'Additional scope',
            'created_by' => $this->user->id,
        ]);

        $this->assertTrue($createResult['success']);
        $this->assertEquals('draft', $createResult['poco']->poco_status);

        // Step 2: Approve PCO
        $approveResult = $this->poChangeOrderService->approvePoChangeOrder(
            $createResult['poco']->poco_id,
            $this->approver->id
        );

        $this->assertTrue($approveResult['success']);
        
        // Step 3: Verify final state
        $history = $this->poChangeOrderService->getPoChangeOrderHistory($this->purchaseOrder->porder_id);
        
        $this->assertCount(1, $history['change_orders']);
        $this->assertEquals(3000.00, $history['total_changes']);
        $this->assertEquals(13000.00, $this->purchaseOrder->fresh()->porder_total_amount);
    }

    /** @test */
    public function it_handles_multiple_pocos_accumulation()
    {
        // First PCO
        $poco1 = PoChangeOrder::create([
            'company_id' => $this->company->id,
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 2000.00,
            'previous_total' => 10000.00,
            'new_total' => 12000.00,
            'poco_description' => 'First change',
            'poco_status' => 'pending_approval',
            'created_by' => $this->user->id,
        ]);

        $this->poChangeOrderService->approvePoChangeOrder($poco1->poco_id, $this->approver->id);

        // Second PCO
        $poco2 = PoChangeOrder::create([
            'company_id' => $this->company->id,
            'purchase_order_id' => $this->purchaseOrder->porder_id,
            'poco_type' => 'amount_change',
            'poco_amount' => 1000.00,
            'previous_total' => 12000.00,
            'new_total' => 13000.00,
            'poco_description' => 'Second change',
            'poco_status' => 'pending_approval',
            'created_by' => $this->user->id,
        ]);

        $this->poChangeOrderService->approvePoChangeOrder($poco2->poco_id, $this->approver->id);

        // Verify accumulation
        $po = $this->purchaseOrder->fresh();
        $this->assertEquals(13000.00, $po->porder_total_amount);
        $this->assertEquals(3000.00, $po->porder_change_orders_total);
    }
}
