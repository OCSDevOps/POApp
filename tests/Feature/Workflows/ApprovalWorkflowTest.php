<?php

namespace Tests\Feature\Workflows;

use Tests\TestCase;
use App\Services\ApprovalService;
use App\Services\BudgetService;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalRequest;
use App\Models\BudgetChangeOrder;
use App\Models\Budget;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\ProjectRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

/**
 * Feature Test: Multi-Level Approval Workflow
 * 
 * Tests the complete workflow of:
 * 1. Creating approval workflows
 * 2. Submitting items for approval
 * 3. Processing approvals through multiple levels
 * 4. Final approval execution
 */
class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected ApprovalService $approvalService;
    protected BudgetService $budgetService;
    protected Company $company;
    protected User $requester;
    protected User $level1Approver;
    protected User $level2Approver;
    protected Project $project;
    protected CostCode $costCode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->approvalService = new ApprovalService();
        $this->budgetService = new BudgetService();
        Queue::fake();
        
        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'subdomain' => 'test',
            'status' => 1,
        ]);

        $this->requester = User::create([
            'name' => 'Requester',
            'email' => 'requester@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $this->level1Approver = User::create([
            'name' => 'Level 1 Approver',
            'email' => 'approver1@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $this->level2Approver = User::create([
            'name' => 'Level 2 Approver',
            'email' => 'approver2@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $this->project = Project::create([
            'proj_name' => 'Test Project',
            'proj_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->costCode = CostCode::create([
            'cc_no' => '01',
            'cc_description' => 'Test Cost Code',
            'cc_level' => 1,
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        // Set company context
        session(['company_id' => $this->company->id]);
        $this->actingAs($this->requester);
    }

    /** @test */
    public function complete_single_level_approval_workflow()
    {
        // ==========================================
        // Step 1: Create Single-Level Workflow
        // ==========================================
        $workflow = ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->level1Approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // ==========================================
        // Step 2: Create Budget Change Order
        // ==========================================
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->requester->id,
        ]);

        $bco = BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'previous_budget' => 10000.00,
            'new_budget' => 15000.00,
            'bco_status' => 'draft',
            'created_by' => $this->requester->id,
        ]);

        // ==========================================
        // Step 3: Submit for Approval
        // ==========================================
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            5000.00,
            $this->requester->id,
            $this->project->proj_id
        );

        $this->assertTrue($submitResult['success']);
        $this->assertNotNull($submitResult['request']);
        $this->assertEquals(1, $submitResult['request']->current_level);
        $this->assertEquals(1, $submitResult['request']->required_levels);
        $this->assertEquals('pending', $submitResult['request']->request_status);

        // Verify BCO status updated
        $this->assertEquals('pending_approval', $bco->fresh()->bco_status);

        // ==========================================
        // Step 4: Level 1 Approver Approves
        // ==========================================
        $this->actingAs($this->level1Approver);

        $approveResult = $this->approvalService->processApproval(
            $submitResult['request']->request_id,
            'approved',
            $this->level1Approver->id,
            $this->level1Approver->name,
            'Approved - within budget'
        );

        $this->assertTrue($approveResult['success']);
        $this->assertTrue($approveResult['final_approval']);

        // ==========================================
        // Step 5: Verify Final State
        // ==========================================
        $request = $submitResult['request']->fresh();
        $this->assertEquals('approved', $request->request_status);
        $this->assertNotNull($request->completed_at);

        // BCO should be approved and budget updated
        $bco->refresh();
        $this->assertEquals('approved', $bco->bco_status);
        $this->assertEquals($this->level1Approver->id, $bco->approved_by);

        $budget->refresh();
        $this->assertEquals(15000.00, $budget->budget_revised_amount);

        // Check approval history
        $history = $this->approvalService->getApprovalHistory('budget_co', $bco->bco_id);
        $this->assertCount(1, $history['history']);
        $this->assertEquals('approved', $history['history'][0]['action']);
    }

    /** @test */
    public function complete_multi_level_approval_workflow()
    {
        // ==========================================
        // Step 1: Create Two-Level Workflow
        // ==========================================
        $workflow1 = ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Level 1',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => 10000,
            'approver_user_ids' => [$this->level1Approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $workflow2 = ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Level 2',
            'workflow_type' => 'budget_co',
            'approval_level' => 2,
            'amount_threshold_min' => 10001,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->level2Approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // ==========================================
        // Step 2: Create Budget Change Order
        // ==========================================
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 50000.00,
            'budget_revised_amount' => 50000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->requester->id,
        ]);

        $bco = BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 25000.00,
            'previous_budget' => 50000.00,
            'new_budget' => 75000.00,
            'bco_status' => 'draft',
            'created_by' => $this->requester->id,
        ]);

        // ==========================================
        // Step 3: Submit for Approval
        // ==========================================
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            25000.00,
            $this->requester->id,
            $this->project->proj_id
        );

        $this->assertEquals(2, $submitResult['request']->required_levels);

        // ==========================================
        // Step 4: Level 1 Approver Approves
        // ==========================================
        $this->actingAs($this->level1Approver);

        $approveResult1 = $this->approvalService->processApproval(
            $submitResult['request']->request_id,
            'approved',
            $this->level1Approver->id,
            $this->level1Approver->name,
            'Level 1 approved'
        );

        $this->assertTrue($approveResult1['success']);
        $this->assertFalse($approveResult1['final_approval'] ?? false);
        $this->assertEquals(2, $approveResult1['next_level']);

        // Request should still be pending
        $request = $submitResult['request']->fresh();
        $this->assertEquals('pending', $request->request_status);
        $this->assertEquals(2, $request->current_level);
        $this->assertEquals($this->level2Approver->id, $request->current_approver_id);

        // BCO should still be pending
        $this->assertEquals('pending_approval', $bco->fresh()->bco_status);

        // ==========================================
        // Step 5: Level 2 Approver Approves
        // ==========================================
        $this->actingAs($this->level2Approver);

        $approveResult2 = $this->approvalService->processApproval(
            $submitResult['request']->request_id,
            'approved',
            $this->level2Approver->id,
            $this->level2Approver->name,
            'Final approval granted'
        );

        $this->assertTrue($approveResult2['success']);
        $this->assertTrue($approveResult2['final_approval']);

        // ==========================================
        // Step 6: Verify Final State
        // ==========================================
        $request = $submitResult['request']->fresh();
        $this->assertEquals('approved', $request->request_status);

        $bco->refresh();
        $this->assertEquals('approved', $bco->bco_status);
        $this->assertEquals($this->level2Approver->id, $bco->approved_by);

        $budget->refresh();
        $this->assertEquals(75000.00, $budget->budget_revised_amount);

        // Check approval history has both approvals
        $history = $this->approvalService->getApprovalHistory('budget_co', $bco->bco_id);
        $this->assertCount(2, $history['history']);
    }

    /** @test */
    public function workflow_rejection_at_level_one()
    {
        // Create workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->level1Approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // Create BCO
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->requester->id,
        ]);

        $bco = BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 50000.00,
            'previous_budget' => 10000.00,
            'new_budget' => 60000.00,
            'bco_status' => 'draft',
            'created_by' => $this->requester->id,
        ]);

        // Submit for approval
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            50000.00,
            $this->requester->id,
            $this->project->proj_id
        );

        // Level 1 rejects
        $this->actingAs($this->level1Approver);

        $rejectResult = $this->approvalService->processApproval(
            $submitResult['request']->request_id,
            'rejected',
            $this->level1Approver->id,
            $this->level1Approver->name,
            'Increase not justified'
        );

        $this->assertTrue($rejectResult['success']);

        // Verify rejection
        $request = $submitResult['request']->fresh();
        $this->assertEquals('rejected', $request->request_status);

        $bco->refresh();
        $this->assertEquals('rejected', $bco->bco_status);
        $this->assertEquals('Increase not justified', $bco->rejection_reason);

        // Budget should not be updated
        $budget->refresh();
        $this->assertEquals(10000.00, $budget->budget_revised_amount);
    }

    /** @test */
    public function workflow_with_role_based_approvers()
    {
        // Create role-based workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Role Based Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_roles' => ['Project Manager', 'Finance Director'],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // Assign roles to users
        ProjectRole::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->proj_id,
            'user_id' => $this->level1Approver->id,
            'role_name' => 'Project Manager',
            'is_active' => true,
        ]);

        // Create BCO
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->requester->id,
        ]);

        $bco = BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'previous_budget' => 10000.00,
            'new_budget' => 15000.00,
            'bco_status' => 'draft',
            'created_by' => $this->requester->id,
        ]);

        // Submit for approval
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            5000.00,
            $this->requester->id,
            $this->project->proj_id
        );

        // Should resolve to user with Project Manager role
        $this->assertTrue($submitResult['approvers']->contains('id', $this->level1Approver->id));
        $this->assertEquals($this->level1Approver->id, $submitResult['request']->current_approver_id);
    }

    /** @test */
    public function workflow_prevents_unauthorized_approval()
    {
        // Create workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->level1Approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // Create unauthorized user
        $unauthorizedUser = User::create([
            'name' => 'Unauthorized',
            'email' => 'unauthorized@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        // Create BCO
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->requester->id,
        ]);

        $bco = BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'previous_budget' => 10000.00,
            'new_budget' => 15000.00,
            'bco_status' => 'draft',
            'created_by' => $this->requester->id,
        ]);

        // Submit for approval
        $submitResult = $this->approvalService->submitForApproval(
            'budget_co',
            $bco->bco_id,
            5000.00,
            $this->requester->id,
            $this->project->proj_id
        );

        // Unauthorized user tries to approve
        $this->actingAs($unauthorizedUser);

        $result = $this->approvalService->processApproval(
            $submitResult['request']->request_id,
            'approved',
            $unauthorizedUser->id,
            $unauthorizedUser->name
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not authorized', $result['error']);

        // Request should still be pending
        $this->assertEquals('pending', $submitResult['request']->fresh()->request_status);
    }

    /** @test */
    public function workflow_gets_pending_approvals_for_user()
    {
        // Create workflows
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget CO Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->level1Approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // Create multiple BCOs
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->requester->id,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $bco = BudgetChangeOrder::create([
                'company_id' => $this->company->id,
                'budget_id' => $budget->budget_id,
                'project_id' => $this->project->proj_id,
                'cost_code_id' => $this->costCode->cc_id,
                'bco_type' => 'increase',
                'bco_amount' => 1000.00 * $i,
                'previous_budget' => 10000.00 + (1000.00 * ($i - 1)),
                'new_budget' => 10000.00 + (1000.00 * $i),
                'bco_status' => 'draft',
                'created_by' => $this->requester->id,
            ]);

            $this->approvalService->submitForApproval(
                'budget_co',
                $bco->bco_id,
                1000.00 * $i,
                $this->requester->id,
                $this->project->proj_id
            );
        }

        // Get pending approvals for level 1 approver
        $pending = $this->approvalService->getPendingApprovalsForUser($this->level1Approver->id);

        $this->assertEquals(3, $pending['count']);
        $this->assertCount(3, $pending['requests']);
    }
}
