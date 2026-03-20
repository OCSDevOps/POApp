<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\BudgetService;
use App\Models\Budget;
use App\Models\BudgetChangeOrder;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\ProjectCostCode;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class BudgetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BudgetService $budgetService;
    protected Company $company;
    protected User $user;
    protected Project $project;
    protected CostCode $costCode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->budgetService = new BudgetService();
        
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

        $this->costCode = CostCode::create([
            'cc_no' => '01',
            'cc_description' => 'Test Cost Code',
            'cc_level' => 1,
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        // Set company context
        session(['company_id' => $this->company->id]);
    }

    // ==========================================
    // assignCostCodesToProject Tests
    // ==========================================

    /** @test */
    public function it_assigns_cost_codes_to_project()
    {
        $result = $this->budgetService->assignCostCodesToProject(
            $this->project->proj_id,
            [$this->costCode->cc_id]
        );

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['assigned']);
        $this->assertDatabaseHas('project_cost_codes', [
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_removes_existing_assignments_not_in_new_list()
    {
        // Create initial assignment
        ProjectCostCode::create([
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        // Create another cost code
        $costCode2 = CostCode::create([
            'cc_no' => '02',
            'cc_description' => 'Another Cost Code',
            'cc_level' => 1,
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        // Assign only the new cost code
        $result = $this->budgetService->assignCostCodesToProject(
            $this->project->proj_id,
            [$costCode2->cc_id]
        );

        $this->assertTrue($result['success']);
        $this->assertDatabaseMissing('project_cost_codes', [
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
        ]);
        $this->assertDatabaseHas('project_cost_codes', [
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $costCode2->cc_id,
        ]);
    }

    /** @test */
    public function it_handles_empty_cost_code_list()
    {
        $result = $this->budgetService->assignCostCodesToProject(
            $this->project->proj_id,
            []
        );

        $this->assertTrue($result['success']);
        $this->assertCount(0, $result['assigned']);
    }

    // ==========================================
    // setupBudget Tests
    // ==========================================

    /** @test */
    public function it_creates_new_budget_for_project_cost_code()
    {
        // First assign cost code to project
        ProjectCostCode::create([
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $result = $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            10000.00,
            $this->user->id
        );

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['budget']);
        $this->assertEquals(10000.00, $result['budget']->budget_original_amount);
        $this->assertEquals(10000.00, $result['budget']->budget_revised_amount);
        $this->assertEquals(0, $result['budget']->budget_committed_amount);
        $this->assertEquals(0, $result['budget']->budget_spent_amount);
    }

    /** @test */
    public function it_creates_budget_change_order_when_updating_existing_budget()
    {
        // First assign cost code to project
        ProjectCostCode::create([
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        // Create initial budget
        $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            10000.00,
            $this->user->id
        );

        // Update budget (should create change order)
        $result = $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            15000.00,
            $this->user->id
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('increase', $result['bco']->bco_type);
        $this->assertEquals(5000.00, $result['bco']->bco_amount);
        $this->assertEquals(10000.00, $result['bco']->previous_budget);
        $this->assertEquals(15000.00, $result['bco']->new_budget);
    }

    /** @test */
    public function it_fails_to_create_budget_when_cost_code_not_assigned()
    {
        $result = $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            10000.00,
            $this->user->id
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not assigned', $result['error']);
    }

    /** @test */
    public function it_creates_decrease_change_order_when_reducing_budget()
    {
        // First assign cost code to project
        ProjectCostCode::create([
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        // Create initial budget
        $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            10000.00,
            $this->user->id
        );

        // Decrease budget
        $result = $this->budgetService->setupBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            8000.00,
            $this->user->id
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('decrease', $result['bco']->bco_type);
        $this->assertEquals(-2000.00, $result['bco']->bco_amount);
    }

    // ==========================================
    // createBudgetChangeOrder Tests
    // ==========================================

    /** @test */
    public function it_creates_budget_change_order()
    {
        // Create budget first
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'budget_committed_amount' => 0,
            'budget_spent_amount' => 0,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $result = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'bco_reason' => 'Additional funding',
            'created_by' => $this->user->id,
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('increase', $result['bco']->bco_type);
        $this->assertEquals(5000.00, $result['bco']->bco_amount);
        $this->assertEquals(10000.00, $result['bco']->previous_budget);
        $this->assertEquals(15000.00, $result['bco']->new_budget);
        $this->assertEquals('draft', $result['bco']->bco_status);
    }

    /** @test */
    public function it_fails_to_create_bco_for_nonexistent_budget()
    {
        $result = $this->budgetService->createBudgetChangeOrder([
            'budget_id' => 99999,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'created_by' => $this->user->id,
        ]);

        $this->assertFalse($result['success']);
    }

    // ==========================================
    // approveBudgetChangeOrder Tests
    // ==========================================

    /** @test */
    public function it_approves_budget_change_order_and_updates_budget()
    {
        // Create budget
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'budget_committed_amount' => 0,
            'budget_spent_amount' => 0,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        // Create BCO
        $bco = BudgetChangeOrder::create([
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

        $approver = User::create([
            'name' => 'Approver',
            'email' => 'approver@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $result = $this->budgetService->approveBudgetChangeOrder($bco->bco_id, $approver->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('approved', $result['bco']->fresh()->bco_status);
        $this->assertEquals(15000.00, $result['budget']->fresh()->budget_revised_amount);
        $this->assertNotNull($result['bco']->approved_at);
        $this->assertEquals($approver->id, $result['bco']->approved_by);
    }

    /** @test */
    public function it_cannot_approve_bco_in_invalid_status()
    {
        // Create budget
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        // Create already approved BCO
        $bco = BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'previous_budget' => 10000.00,
            'new_budget' => 15000.00,
            'bco_status' => 'approved',
            'created_by' => $this->user->id,
            'approved_by' => $this->user->id,
            'approved_at' => now(),
        ]);

        $result = $this->budgetService->approveBudgetChangeOrder($bco->bco_id, $this->user->id);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('cannot be approved', $result['error']);
    }

    // ==========================================
    // validatePoBudget Tests
    // ==========================================

    /** @test */
    public function it_validates_po_against_budget_with_sufficient_funds()
    {
        Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'budget_committed_amount' => 2000.00,
            'budget_spent_amount' => 1000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $result = $this->budgetService->validatePoBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            5000.00
        );

        $this->assertTrue($result['valid']);
        $this->assertEquals(10000.00, $result['budget_amount']);
        $this->assertEquals(2000.00, $result['committed']);
        $this->assertEquals(8000.00, $result['available']);
        $this->assertEquals(3000.00, $result['remaining_after_po']);
    }

    /** @test */
    public function it_rejects_po_when_budget_exceeded()
    {
        Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'budget_committed_amount' => 8000.00,
            'budget_spent_amount' => 1000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $result = $this->budgetService->validatePoBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            2000.00
        );

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('exceeds available budget', $result['reason']);
        $this->assertEquals(1000.00, $result['shortfall']);
    }

    /** @test */
    public function it_rejects_po_when_no_budget_exists()
    {
        $result = $this->budgetService->validatePoBudget(
            $this->project->proj_id,
            $this->costCode->cc_id,
            5000.00
        );

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('No budget found', $result['reason']);
    }

    // ==========================================
    // updateBudgetCommitment Tests
    // ==========================================

    /** @test */
    public function it_updates_budget_commitment()
    {
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'budget_committed_amount' => 0,
            'budget_spent_amount' => 0,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $this->budgetService->updateBudgetCommitment(
            $this->project->proj_id,
            $this->costCode->cc_id,
            5000.00
        );

        $this->assertEquals(5000.00, $budget->fresh()->budget_committed_amount);
    }

    /** @test */
    public function it_handles_nonexistent_budget_when_updating_commitment()
    {
        // Should not throw error
        $this->budgetService->updateBudgetCommitment(
            $this->project->proj_id,
            $this->costCode->cc_id,
            5000.00
        );

        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    // ==========================================
    // updateJobCostActual Tests
    // ==========================================

    /** @test */
    public function it_updates_job_cost_actual()
    {
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'budget_committed_amount' => 5000.00,
            'budget_spent_amount' => 0,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $this->budgetService->updateJobCostActual(
            $this->project->proj_id,
            $this->costCode->cc_id,
            3000.00
        );

        $this->assertEquals(3000.00, $budget->fresh()->budget_spent_amount);
    }

    // ==========================================
    // getProjectBudgetSummary Tests
    // ==========================================

    /** @test */
    public function it_gets_project_budget_summary()
    {
        // Create multiple budgets for the project
        Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 12000.00,
            'budget_committed_amount' => 5000.00,
            'budget_spent_amount' => 3000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $costCode2 = CostCode::create([
            'cc_no' => '02',
            'cc_description' => 'Another Cost Code',
            'cc_level' => 1,
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $costCode2->cc_id,
            'budget_original_amount' => 5000.00,
            'budget_revised_amount' => 5000.00,
            'budget_committed_amount' => 2000.00,
            'budget_spent_amount' => 1000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        $result = $this->budgetService->getProjectBudgetSummary($this->project->proj_id);

        $this->assertEquals(17000.00, $result['total_budget']);
        $this->assertEquals(15000.00, $result['total_original']);
        $this->assertEquals(7000.00, $result['total_committed']);
        $this->assertEquals(4000.00, $result['total_actual']);
        $this->assertEquals(10000.00, $result['total_available']);
        $this->assertEquals(13000.00, $result['total_variance']);
        $this->assertCount(2, $result['budgets_by_cost_code']);
    }

    // ==========================================
    // getBudgetChangeOrderHistory Tests
    // ==========================================

    /** @test */
    public function it_gets_budget_change_order_history()
    {
        $budget = Budget::create([
            'budget_project_id' => $this->project->proj_id,
            'budget_cost_code_id' => $this->costCode->cc_id,
            'budget_original_amount' => 10000.00,
            'budget_revised_amount' => 10000.00,
            'company_id' => $this->company->id,
            'budget_created_by' => $this->user->id,
        ]);

        // Create approved BCO
        BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'increase',
            'bco_amount' => 5000.00,
            'previous_budget' => 10000.00,
            'new_budget' => 15000.00,
            'bco_status' => 'approved',
            'created_by' => $this->user->id,
            'approved_by' => $this->user->id,
            'approved_at' => now(),
        ]);

        // Create pending BCO
        BudgetChangeOrder::create([
            'company_id' => $this->company->id,
            'budget_id' => $budget->budget_id,
            'project_id' => $this->project->proj_id,
            'cost_code_id' => $this->costCode->cc_id,
            'bco_type' => 'decrease',
            'bco_amount' => -2000.00,
            'previous_budget' => 15000.00,
            'new_budget' => 13000.00,
            'bco_status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $result = $this->budgetService->getBudgetChangeOrderHistory($this->project->proj_id);

        $this->assertCount(2, $result['change_orders']);
        $this->assertEquals(5000.00, $result['total_increase']);
        $this->assertEquals(0, $result['total_decrease']); // Decrease is not approved
        $this->assertEquals(1, $result['pending_count']);
    }
}
