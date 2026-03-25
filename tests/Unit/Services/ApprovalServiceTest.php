<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ApprovalService;
use App\Services\BudgetService;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalRequest;
use App\Models\Budget;
use App\Models\BudgetChangeOrder;
use App\Models\PoChangeOrder;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\ProjectRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;

class ApprovalServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected ApprovalService $approvalService;
    protected Company $company;
    protected User $user;
    protected User $approver;
    protected Project $project;
    protected CostCode $costCode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->approvalService = new ApprovalService();
        Queue::fake();
        Notification::fake();
        
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

        $this->costCode = CostCode::create([
            'cc_no' => '01',
            'cc_description' => 'Test Cost Code',
            'cc_level' => 1,
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        // Set company context and authenticate
        session(['company_id' => $this->company->id]);
        $this->actingAs($this->user);
    }

    // ==========================================
    // Submit for Approval Tests
    // ==========================================

    /** @test */
    public function it_auto_approves_when_no_workflow_exists()
    {
        $budget = $this->createBudget(10000);

        $result = $this->approvalService->submitForApproval(
            'budget',
            $budget->budget_id,
            10000,
            $this->user->id,
            $this->project->proj_id
        );

        $this->assertTrue($result['success']);
        $this->assertTrue($result['auto_approved']);
        $this->assertStringContainsString('automatically approved', $result['message']);
    }

    /** @test */
    public function it_submits_entity_for_approval_with_single_level_workflow()
    {
        // Create workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget Approval',
            'workflow_type' => 'budget',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $budget = $this->createBudget(10000);

        $result = $this->approvalService->submitForApproval(
            'budget',
            $budget->budget_id,
            10000,
            $this->user->id,
            $this->project->proj_id
        );

        $this->assertTrue($result['success']);
        $this->assertFalse($result['auto_approved'] ?? true);
        $this->assertNotNull($result['request']);
        $this->assertEquals(1, $result['request']->current_level);
        $this->assertEquals(1, $result['request']->required_levels);
        $this->assertEquals($this->approver->id, $result['request']->current_approver_id);
    }

    /** @test */
    public function it_submits_entity_for_approval_with_multi_level_workflow()
    {
        $approver2 = User::create([
            'name' => 'Approver 2',
            'email' => 'approver2@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        // Create multi-level workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget Approval Level 1',
            'workflow_type' => 'budget',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => 5000,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget Approval Level 2',
            'workflow_type' => 'budget',
            'approval_level' => 2,
            'amount_threshold_min' => 5001,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$approver2->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $budget = $this->createBudget(10000);

        $result = $this->approvalService->submitForApproval(
            'budget',
            $budget->budget_id,
            10000,
            $this->user->id,
            $this->project->proj_id
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['request']->required_levels);
    }

    /** @test */
    public function it_uses_project_specific_workflow_over_company_workflow()
    {
        // Create company-wide workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Company Budget Approval',
            'workflow_type' => 'budget',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [999], // Different approver
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // Create project-specific workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Project Budget Approval',
            'workflow_type' => 'budget',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
            'project_id' => $this->project->proj_id,
        ]);

        $budget = $this->createBudget(10000);

        $result = $this->approvalService->submitForApproval(
            'budget',
            $budget->budget_id,
            10000,
            $this->user->id,
            $this->project->proj_id
        );

        $this->assertEquals($this->approver->id, $result['request']->current_approver_id);
    }

    /** @test */
    public function it_resolves_approvers_from_project_roles()
    {
        // Create role-based workflow
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Role Based Approval',
            'workflow_type' => 'budget',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_roles' => ['Project Manager'],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        // Create project role
        ProjectRole::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->proj_id,
            'user_id' => $this->approver->id,
            'role_name' => 'Project Manager',
            'is_active' => true,
        ]);

        $budget = $this->createBudget(10000);

        $result = $this->approvalService->submitForApproval(
            'budget',
            $budget->budget_id,
            10000,
            $this->user->id,
            $this->project->proj_id
        );

        $this->assertTrue($result['success']);
        $this->assertTrue($result['approvers']->contains('id', $this->approver->id));
    }

    // ==========================================
    // Process Approval Tests
    // ==========================================

    /** @test */
    public function it_processes_single_level_approval()
    {
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $bco = $this->createBudgetChangeOrder();

        $request = ApprovalRequest::create([
            'company_id' => $this->company->id,
            'workflow_id' => 1,
            'request_type' => 'budget_co',
            'entity_id' => $bco->bco_id,
            'entity_number' => $bco->bco_number,
            'request_amount' => 5000,
            'current_level' => 1,
            'required_levels' => 1,
            'request_status' => 'pending',
            'requested_by' => $this->user->id,
            'submitted_at' => now(),
        ]);

        $result = $this->approvalService->processApproval(
            $request->request_id,
            'approved',
            $this->approver->id,
            $this->approver->name,
            'Looks good'
        );

        $this->assertTrue($result['success']);
        $this->assertTrue($result['final_approval']);
        $this->assertEquals('approved', $request->fresh()->request_status);
    }

    /** @test */
    public function it_advances_to_next_level_in_multi_level_approval()
    {
        $approver2 = User::create([
            'name' => 'Approver 2',
            'email' => 'approver2@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $workflow1 = ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Level 1',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Level 2',
            'workflow_type' => 'budget_co',
            'approval_level' => 2,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$approver2->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $bco = $this->createBudgetChangeOrder();

        $request = ApprovalRequest::create([
            'company_id' => $this->company->id,
            'workflow_id' => $workflow1->workflow_id,
            'request_type' => 'budget_co',
            'entity_id' => $bco->bco_id,
            'entity_number' => $bco->bco_number,
            'request_amount' => 5000,
            'current_level' => 1,
            'required_levels' => 2,
            'request_status' => 'pending',
            'requested_by' => $this->user->id,
            'submitted_at' => now(),
        ]);

        $result = $this->approvalService->processApproval(
            $request->request_id,
            'approved',
            $this->approver->id,
            $this->approver->name
        );

        $this->assertTrue($result['success']);
        $this->assertFalse($result['final_approval'] ?? false);
        $this->assertEquals(2, $result['next_level']);
        $this->assertEquals(2, $request->fresh()->current_level);
        $this->assertEquals('pending', $request->fresh()->request_status);
    }

    /** @test */
    public function it_processes_rejection()
    {
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $bco = $this->createBudgetChangeOrder();

        $request = ApprovalRequest::create([
            'company_id' => $this->company->id,
            'workflow_id' => 1,
            'request_type' => 'budget_co',
            'entity_id' => $bco->bco_id,
            'entity_number' => $bco->bco_number,
            'request_amount' => 5000,
            'current_level' => 1,
            'required_levels' => 1,
            'request_status' => 'pending',
            'requested_by' => $this->user->id,
            'submitted_at' => now(),
        ]);

        $result = $this->approvalService->processApproval(
            $request->request_id,
            'rejected',
            $this->approver->id,
            $this->approver->name,
            'Budget not justified'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('rejected', $request->fresh()->request_status);
        $this->assertEquals('rejected', $bco->fresh()->bco_status);
    }

    /** @test */
    public function it_prevents_unauthorized_user_from_approving()
    {
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $bco = $this->createBudgetChangeOrder();

        $request = ApprovalRequest::create([
            'company_id' => $this->company->id,
            'workflow_id' => 1,
            'request_type' => 'budget_co',
            'entity_id' => $bco->bco_id,
            'entity_number' => $bco->bco_number,
            'request_amount' => 5000,
            'current_level' => 1,
            'required_levels' => 1,
            'request_status' => 'pending',
            'requested_by' => $this->user->id,
            'submitted_at' => now(),
        ]);

        $unauthorizedUser = User::create([
            'name' => 'Unauthorized',
            'email' => 'unauthorized@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $result = $this->approvalService->processApproval(
            $request->request_id,
            'approved',
            $unauthorizedUser->id,
            $unauthorizedUser->name
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not authorized', $result['error']);
    }

    /** @test */
    public function it_prevents_approving_non_pending_request()
    {
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget Approval',
            'workflow_type' => 'budget_co',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $bco = $this->createBudgetChangeOrder();

        $request = ApprovalRequest::create([
            'company_id' => $this->company->id,
            'workflow_id' => 1,
            'request_type' => 'budget_co',
            'entity_id' => $bco->bco_id,
            'entity_number' => $bco->bco_number,
            'request_amount' => 5000,
            'current_level' => 1,
            'required_levels' => 1,
            'request_status' => 'approved', // Already approved
            'requested_by' => $this->user->id,
            'submitted_at' => now(),
            'completed_at' => now(),
        ]);

        $result = $this->approvalService->processApproval(
            $request->request_id,
            'approved',
            $this->approver->id,
            $this->approver->name
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not in pending status', $result['error']);
    }

    // ==========================================
    // Get Pending Approvals Tests
    // ==========================================

    /** @test */
    public function it_gets_pending_approvals_for_user()
    {
        ApprovalWorkflow::create([
            'company_id' => $this->company->id,
            'workflow_name' => 'Budget Approval',
            'workflow_type' => 'budget',
            'approval_level' => 1,
            'amount_threshold_min' => 0,
            'amount_threshold_max' => null,
            'approver_user_ids' => [$this->approver->id],
            'approval_logic' => 'any',
            'is_active' => true,
        ]);

        $budget = $this->createBudget(10000);

        // Create pending request for approver
        ApprovalRequest::create([
            'company_id' => $this->company->id,
            'workflow_id' => 1,
            'request_type' => 'budget',
            'entity_id' => $budget->budget_id,
            'entity_number' => 'BUDGET-001',
            'request_amount' => 10000,
            'current_level' => 1,
            'required_levels' => 1,
            'request_status' => 'pending',
            'requested_by' => $this->user->id,
            'current_approver_id' => $this->approver->id,
            'submitted_at' => now(),
        ]);

        $result = $this->approvalService->getPendingApprovalsForUser($this->approver->id);

        $this->assertCount(1, $result['requests']);
        $this->assertEquals(1, $result['count']);
    }

    // ==========================================
    // Get Approval History Tests
    // ==========================================

    /** @test */
    public function it_gets_approval_history_for_entity()
    {
        $budget = $this->createBudget(10000);

        ApprovalRequest::create([
            'company_id' => $this->company->id,
            'workflow_id' => 1,
            'request_type' => 'budget',
            'entity_id' => $budget->budget_id,
            'entity_number' => 'BUDGET-001',
            'request_amount' => 10000,
            'current_level' => 1,
            'required_levels' => 1,
            'request_status' => 'approved',
            'requested_by' => $this->user->id,
            'submitted_at' => now(),
            'completed_at' => now(),
            'approval_history' => [
                [
                    'action' => 'approved',
                    'user_id' => $this->approver->id,
                    'user_name' => $this->approver->name,
                    'level' => 1,
                    'comments' => 'Approved',
                    'timestamp' => now()->toIso8601String(),
                ],
            ],
        ]);

        $result = $this->approvalService->getApprovalHistory('budget', $budget->budget_id);

        $this->assertNotNull($result['request']);
        $this->assertCount(1, $result['history']);
        $this->assertEquals('approved', $result['current_status']);
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    private function createBudget($amount)
    {
        return Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => $amount,
            'budget_revised_amount' => $amount,
            'budget_committed_amount' => 0,
            'budget_spent_amount' => 0,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);
    }

    private function createBudgetChangeOrder()
    {
        $budget = $this->createBudget(10000);

        return BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'previous_budget' => 10000.00,
            'new_budget' => 15000.00,
            'bco_status' => 'pending_approval',
            'created_by' => $this->user->id,
        ]);
    }
}
