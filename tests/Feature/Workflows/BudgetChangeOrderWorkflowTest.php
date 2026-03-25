<?php

namespace Tests\Feature\Workflows;

use Tests\TestCase;
use App\Services\BudgetService;
use App\Services\ApprovalService;
use App\Models\Budget;
use App\Models\BudgetChangeOrder;
use App\Models\ApprovalWorkflow;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\ProjectCostCode;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;

/**
 * Feature Test: Budget Change Order Workflow
 * 
 * Tests the complete workflow of:
 * 1. Setting up initial project budget
 * 2. Creating a budget change order
 * 3. Submitting BCO for approval
 * 4. Approving BCO through workflow
 * 5. Verifying budget is updated
 * 6. Creating additional BCOs (increase and decrease)
 */
class BudgetChangeOrderWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected BudgetService $budgetService;
    protected ApprovalService $approvalService;
    protected Company $company;
    protected User $budgetManager;
    protected User $approver;
    protected Project $project;
    protected CostCode $costCode;
    protected Budget $budget;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->budgetService = new BudgetService();
        $this->approvalService = new ApprovalService();
        Queue::fake();
        
        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'subdomain' => 'test',
            'status' => 1,
        ]);

        $this->budgetManager = User::create([
            'name' => 'Budget Manager',
            'email' => 'budget@test.com',
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
            'proj_name' => 'Construction Project',
            'proj_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->costCode = CostCode::create([
            'cc_no' => '03',
            'cc_description' => 'Concrete & Masonry',
            'cc_level' => 1,
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        // Setup initial project budget
        ProjectCostCode::create([
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $budgetResult = $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            100000.00, // $100,000 initial budget
            $this->budgetManager->id
        );

        $this->budget = $budgetResult['budget'];

        // Set company context
        session(['company_id' => $this->company->id]);
        $this->actingAs($this->budgetManager);
    }

    /** @test */
    public function complete_bco_increase_workflow_with_approval()
    {
        // ==========================================
        // Step 1: Verify Initial Budget
        // ==========================================
        $this->assertEquals(100000.00, $this->budget->budget_original_amount);
        $this->assertEquals(100000.00, $this->budget->budget_revised_amount);
        $this->assertEquals(0, $this->budget->budget_committed_amount);

        // ==========================================
        // Step 2: Create Budget Change Order (Increase)
        // ==========================================
        $bcoResult = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $this->budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 25000.00,
            'bco_reason' => 'Additional scope - foundation expansion',
            'bco_notes' => 'Client requested additional basement area',
            'bco_reference' => 'CLIENT-CHANGE-001',
            'created_by' => $this->budgetManager->id,
        ]);

        $this->assertTrue($bcoResult['success']);
        $bco = $bcoResult['bco'];

        $this->assertEquals('increase', $bco->bco_type);
        $this->assertEquals(25000.00, $bco->bco_amount);
        $this->assertEquals(100000.00, $bco->previous_budget);
        $this->assertEquals(125000.00, $bco->new_budget);
        $this->assertEquals('draft', $bco->bco_status);

        // Verify BCO appears in history
        $history = $this->budgetService->getBudgetChangeOrderHistory($this->project->proj_id);
        $this->assertCount(1, $history['change_orders']);
        $this->assertEquals(1, $history['pending_count']);

        // ==========================================
        // Step 3: Create Approval Workflow
        // ==========================================
        $workflow = ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // ==========================================
        // Step 4: Submit BCO for Approval
        // ==========================================
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            25000.00,
            $this->budgetManager->id,
            $this->project->proj_id
        );

        $this->assertTrue($submitResult['success']);
        $this->assertNotNull($submitResult['request']);

        // BCO status should be pending_approval
        $bco->refresh();
        $this->assertEquals('pending_approval', $bco->bco_status);

        // ==========================================
        // Step 5: Approve BCO
        // ==========================================
        $this->actingAs($this->approver);

        $approveResult = $this->approvalService->processApproval(
            $submitResult['request']->request_id,
            'approved',
            $this->approver->id,
            $this->approver->name,
            'Approved - within project contingency'
        );

        $this->assertTrue($approveResult['success']);
        $this->assertTrue($approveResult['final_approval']);

        // ==========================================
        // Step 6: Verify Budget Updated
        // ==========================================
        $this->budget->refresh();
        $this->assertEquals(125000.00, $this->budget->budget_revised_amount);
        $this->assertEquals(100000.00, $this->budget->budget_original_amount);

        // BCO should be approved
        $bco->refresh();
        $this->assertEquals('approved', $bco->bco_status);
        $this->assertEquals($this->approver->id, $bco->approved_by);
        $this->assertNotNull($bco->approved_at);

        // ==========================================
        // Step 7: Verify History Updated
        // ==========================================
        $history = $this->budgetService->getBudgetChangeOrderHistory($this->project->proj_id);
        $this->assertEquals(25000.00, $history['total_increase']);
        $this->assertEquals(0, $history['pending_count']);

        // ==========================================
        // Step 8: Verify Budget Summary
        // ==========================================
        $summary = $this->budgetService->getProjectBudgetSummary($this->project->proj_id);
        $this->assertEquals(125000.00, $summary['total_budget']);
        $this->assertEquals(100000.00, $summary['total_original']);
    }

    /** @test */
    public function bco_decrease_workflow_reduces_budget()
    {
        // ==========================================
        // Step 1: Create Approval Workflow
        // ==========================================
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // ==========================================
        // Step 2: Create Budget Change Order (Decrease)
        // ==========================================
        $bcoResult = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $this->budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'decrease',
            'bco_amount' => -15000.00,
            'bco_reason' => 'Value engineering - optimized design',
            'bco_notes' => 'Reduced concrete requirements due to design optimization',
            'created_by' => $this->budgetManager->id,
        ]);

        $this->assertTrue($bcoResult['success']);
        $bco = $bcoResult['bco'];

        $this->assertEquals('decrease', $bco->bco_type);
        $this->assertEquals(-15000.00, $bco->bco_amount);
        $this->assertEquals(85000.00, $bco->new_budget);

        // ==========================================
        // Step 3: Submit and Approve
        // ==========================================
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            15000.00,
            $this->budgetManager->id,
            $this->project->proj_id
        );

        $this->actingAs($this->approver);
        $this->approvalService->processApproval(
            $submitResult['request']->request_id,
            'approved',
            $this->approver->id,
            $this->approver->name,
            'Approved - good cost savings'
        );

        // ==========================================
        // Step 4: Verify Budget Decreased
        // ==========================================
        $this->budget->refresh();
        $this->assertEquals(85000.00, $this->budget->budget_revised_amount);
        $this->assertEquals(100000.00, $this->budget->budget_original_amount);

        // History should show decrease
        $history = $this->budgetService->getBudgetChangeOrderHistory($this->project->proj_id);
        $this->assertEquals(0, $history['total_increase']);
        $this->assertEquals(-15000.00, $history['total_decrease']);
    }

    /** @test */
    public function multiple_bcos_accumulate_correctly()
    {
        // Create approval workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // Create and approve first BCO (increase)
        $bco1 = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $this->budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 20000.00,
            'bco_reason' => 'First change',
            'created_by' => $this->budgetManager->id,
        ]);

        $submit1 = $this->approvalService->submitForApproval(
            'budget_co',
            $bco1['bco']->bco_id,
            20000.00,
            $this->budgetManager->id,
            $this->project->proj_id
        );

        $this->actingAs($this->approver);
        $this->approvalService->processApproval(
            $submit1['request']->request_id,
            'approved',
            $this->approver->id,
            $this->approver->name
        );

        $this->budget->refresh();
        $this->assertEquals(120000.00, $this->budget->budget_revised_amount);

        // Create and approve second BCO (increase)
        $this->actingAs($this->budgetManager);
        $bco2 = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $this->budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 10000.00,
            'bco_reason' => 'Second change',
            'created_by' => $this->budgetManager->id,
        ]);

        $submit2 = $this->approvalService->submitForApproval(
            'budget_co',
            $bco2['bco']->bco_id,
            10000.00,
            $this->budgetManager->id,
            $this->project->proj_id
        );

        $this->actingAs($this->approver);
        $this->approvalService->processApproval(
            $submit2['request']->request_id,
            'approved',
            $this->approver->id,
            $this->approver->name
        );

        $this->budget->refresh();
        $this->assertEquals(130000.00, $this->budget->budget_revised_amount);

        // Create and approve third BCO (decrease)
        $this->actingAs($this->budgetManager);
        $bco3 = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $this->budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'decrease',
            'bco_amount' => -5000.00,
            'bco_reason' => 'Cost savings',
            'created_by' => $this->budgetManager->id,
        ]);

        $submit3 = $this->approvalService->submitForApproval(
            'budget_co',
            $bco3['bco']->bco_id,
            5000.00,
            $this->budgetManager->id,
            $this->project->proj_id
        );

        $this->actingAs($this->approver);
        $this->approvalService->processApproval(
            $submit3['request']->request_id,
            'approved',
            $this->approver->id,
            $this->approver->name
        );

        // ==========================================
        // Verify Final Budget
        // ==========================================
        $this->budget->refresh();
        $this->assertEquals(125000.00, $this->budget->budget_revised_amount);

        // Verify history
        $history = $this->budgetService->getBudgetChangeOrderHistory($this->project->proj_id);
        $this->assertCount(3, $history['change_orders']);
        $this->assertEquals(30000.00, $history['total_increase']);
        $this->assertEquals(-5000.00, $history['total_decrease']);
    }

    /** @test */
    public function rejected_bco_does_not_affect_budget()
    {
        // Create approval workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // Create BCO
        $bcoResult = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $this->budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 50000.00,
            'bco_reason' => 'Excessive increase',
            'created_by' => $this->budgetManager->id,
        ]);

        $bco = $bcoResult['bco'];

        // Submit for approval
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            50000.00,
            $this->budgetManager->id,
            $this->project->proj_id
        );

        // Reject the BCO
        $this->actingAs($this->approver);
        $this->approvalService->processApproval(
            $submitResult['request']->request_id,
            'rejected',
            $this->approver->id,
            $this->approver->name,
            'Increase not justified'
        );

        // Budget should remain unchanged
        $this->budget->refresh();
        $this->assertEquals(100000.00, $this->budget->budget_revised_amount);

        // BCO should be rejected
        $bco->refresh();
        $this->assertEquals('rejected', $bco->bco_status);

        // History should show no approved changes
        $history = $this->budgetService->getBudgetChangeOrderHistory($this->project->proj_id);
        $this->assertEquals(0, $history['total_increase']);
    }

    /** @test */
    public function bco_auto_approved_when_no_workflow_exists()
    {
        // Create BCO without any workflow
        $bcoResult = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $this->budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'bco_reason' => 'Minor adjustment',
            'created_by' => $this->budgetManager->id,
        ]);

        $bco = $bcoResult['bco'];

        // Submit for approval (should auto-approve)
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            5000.00,
            $this->budgetManager->id,
            $this->project->proj_id
        );

        $this->assertTrue($submitResult['success']);
        $this->assertTrue($submitResult['auto_approved']);

        // Budget should be updated immediately
        $this->budget->refresh();
        $this->assertEquals(105000.00, $this->budget->budget_revised_amount);

        // BCO should be approved
        $bco->refresh();
        $this->assertEquals('approved', $bco->bco_status);
    }
}
