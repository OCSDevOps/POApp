<?php

namespace Tests\Feature\Workflows;

use Tests\TestCase;
use App\Services\BudgetService;
use App\Services\PurchaseOrderService;
use App\Services\ApprovalService;
use App\Models\Budget;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\ProjectCostCode;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

/**
 * Feature Test: Purchase Order Creation with Budget Commitment
 * 
 * Tests the complete workflow of:
 * 1. Setting up a project budget
 * 2. Creating a PO against that budget
 * 3. Verifying budget commitment
 * 4. Receiving goods and updating actuals
 */
class PurchaseOrderBudgetWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected BudgetService $budgetService;
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
        
        $this->budgetService = new BudgetService();
        $this->poService = new PurchaseOrderService();
        Queue::fake();
        
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
            'proj_name' => 'Construction Project',
            'proj_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->supplier = Supplier::create([
            'sup_name' => 'Building Supplies Inc',
            'sup_email' => 'sales@supplier.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->costCode = CostCode::create([
            'cc_no' => '03',
            'cc_description' => 'Concrete & Masonry',
            'cc_level' => 1,
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->item = Item::create([
            'item_code' => 'CONC-001',
            'item_name' => 'Ready Mix Concrete',
            'item_ccode_ms' => $this->costCode->cc_id,
            'item_status' => 1,
            'company_id' => $this->company->id,
        ]);

        // Set company context
        session(['company_id' => $this->company->id]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function complete_workflow_creating_po_with_budget_commitment()
    {
        // ==========================================
        // Step 1: Assign Cost Code to Project
        // ==========================================
        $assignmentResult = $this->budgetService->assignCostCodesToProject(
            $this->project->proj_id,
            [$this->costCode->cc_id]
        );

        $this->assertTrue($assignmentResult['success']);
        $this->assertDatabaseHas('project_cost_codes', [
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
        ]);

        // ==========================================
        // Step 2: Setup Project Budget
        // ==========================================
        $budgetResult = $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            50000.00, // $50,000 budget
            $this->user->id
        );

        $this->assertTrue($budgetResult['success']);
        $budget = $budgetResult['budget'];
        $this->assertEquals(50000.00, $budget->budget_revised_amount);

        // ==========================================
        // Step 3: Validate PO Against Budget
        // ==========================================
        $poAmount = 15000.00;
        $validation = $this->budgetService->validatePoBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            $poAmount
        );

        $this->assertTrue($validation['valid']);
        $this->assertEquals(50000.00, $validation['budget_amount']);
        $this->assertEquals(50000.00, $validation['available']);
        $this->assertEquals(35000.00, $validation['remaining_after_po']);

        // ==========================================
        // Step 4: Create Purchase Order
        // ==========================================
        config(['app.budget_constraints_enabled' => false]);

        $po = $this->poService->createPurchaseOrder([
            'project_id' => $this->project->proj_id,
            'supplier_id' => $this->supplier->sup_id,
            'address' => '123 Construction Site',
            'description' => 'Concrete for foundation',
        ], [
            [
                'item_code' => $this->item->item_code,
                'sku' => 'CONC-001-SKU',
                'quantity' => 100,
                'unit_price' => 150.00,
                'tax_rate' => 0,
            ],
        ]);

        $this->assertNotNull($po);
        $this->assertEquals(15000.00, $po->porder_total_amount);

        // ==========================================
        // Step 5: Update Budget Commitment
        // ==========================================
        $this->budgetService->updateBudgetCommitment(
            $this->project->proj_id,
            $this->costCode->cc_id,
            $po->porder_total_amount
        );

        $budget->refresh();
        $this->assertEquals(15000.00, $budget->budget_committed_amount);

        // ==========================================
        // Step 6: Verify Budget Summary
        // ==========================================
        $summary = $this->budgetService->getProjectBudgetSummary($this->project->proj_id);

        $this->assertEquals(50000.00, $summary['total_budget']);
        $this->assertEquals(15000.00, $summary['total_committed']);
        $this->assertEquals(35000.00, $summary['total_available']);

        // ==========================================
        // Step 7: Receive Order (Partial)
        // ==========================================
        $receiveOrder = $this->poService->createReceiveOrder(
            $po->porder_id,
            'GRN-001',
            [
                [
                    'item_code' => $this->item->item_code,
                    'quantity' => 80, // Partial receipt
                ],
            ],
            now()->toDateString()
        );

        $this->assertNotNull($receiveOrder);

        // ==========================================
        // Step 8: Update Budget Actuals
        // ==========================================
        $actualAmount = 80 * 150.00; // 80 units @ $150
        $this->budgetService->updateJobCostActual(
            $this->project->proj_id,
            $this->costCode->cc_id,
            $actualAmount
        );

        $budget->refresh();
        $this->assertEquals(12000.00, $budget->budget_spent_amount);

        // ==========================================
        // Step 9: Create Second PO - Verify Budget Tracking
        // ==========================================
        $po2 = $this->poService->createPurchaseOrder([
            'project_id' => $this->project->proj_id,
            'supplier_id' => $this->supplier->sup_id,
            'description' => 'Additional concrete',
        ], [
            [
                'item_code' => $this->item->item_code,
                'quantity' => 100,
                'unit_price' => 150.00,
                'tax_rate' => 0,
            ],
        ]);

        $this->budgetService->updateBudgetCommitment(
            $this->project->proj_id,
            $this->costCode->cc_id,
            $po2->porder_total_amount
        );

        $budget->refresh();
        $this->assertEquals(30000.00, $budget->budget_committed_amount); // 15000 + 15000
        $this->assertEquals(12000.00, $budget->budget_spent_amount);

        // ==========================================
        // Step 10: Final Budget Verification
        // ==========================================
        $finalSummary = $this->budgetService->getProjectBudgetSummary($this->project->proj_id);

        $this->assertEquals(50000.00, $finalSummary['total_budget']);
        $this->assertEquals(30000.00, $finalSummary['total_committed']);
        $this->assertEquals(12000.00, $finalSummary['total_actual']);
        $this->assertEquals(20000.00, $finalSummary['total_available']);
        $this->assertEquals(38000.00, $finalSummary['total_variance']); // 50000 - 12000

        // Verify utilization calculation
        $this->assertEquals(84, $budget->fresh()->utilization_percent); // (30000 + 12000) / 50000 * 100
    }

    /** @test */
    public function workflow_prevents_po_exceeding_budget()
    {
        // Setup budget
        ProjectCostCode::create([
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            10000.00,
            $this->user->id
        );

        // Try to validate PO that exceeds budget
        $validation = $this->budgetService->validatePoBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            15000.00
        );

        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('exceeds available budget', $validation['reason']);
    }

    /** @test */
    public function workflow_handles_budget_change_order_mid_project()
    {
        // Setup initial budget
        ProjectCostCode::create([
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $budgetResult = $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            10000.00,
            $this->user->id
        );

        $budget = $budgetResult['budget'];

        // Create PO consuming most budget
        config(['app.budget_constraints_enabled' => false]);
        $po = $this->poService->createPurchaseOrder([
            'project_id' => $this->project->proj_id,
            'supplier_id' => $this->supplier->sup_id,
        ], [
            [
                'item_code' => $this->item->item_code,
                'quantity' => 50,
                'unit_price' => 100.00,
                'tax_rate' => 0,
            ],
        ]);

        $this->budgetService->updateBudgetCommitment(
            $this->project->proj_id,
            $this->costCode->cc_id,
            5000.00
        );

        // Create budget change order to increase budget
        $bcoResult = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 10000.00,
            'bco_reason' => 'Additional scope identified',
            'created_by' => $this->user->id,
        ]);

        $this->assertTrue($bcoResult['success']);

        // Approve BCO
        $approvalResult = $this->budgetService->approveBudgetChangeOrder(
            $bcoResult['bco']->bco_id,
            $this->user->id
        );

        $this->assertTrue($approvalResult['success']);

        // Verify budget increased
        $budget->refresh();
        $this->assertEquals(20000.00, $budget->budget_revised_amount);

        // Now additional PO should be valid
        $validation = $this->budgetService->validatePoBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            8000.00
        );

        $this->assertTrue($validation['valid']);
        $this->assertEquals(15000.00, $validation['available']); // 20000 - 5000
    }
}
