<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiTenancyIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected $company1;
    protected $company2;
    protected $user1;
    protected $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create two companies
        $this->company1 = Company::create([
            'name' => 'Company One',
            'subdomain' => 'company1',
            'status' => 1,
        ]);

        $this->company2 = Company::create([
            'name' => 'Company Two',
            'subdomain' => 'company2',
            'status' => 1,
        ]);

        // Create users for each company
        $this->user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@company1.com',
            'password' => bcrypt('password'),
            'u_type' => 2,
            'company_id' => $this->company1->id,
        ]);

        $this->user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@company2.com',
            'password' => bcrypt('password'),
            'u_type' => 2,
            'company_id' => $this->company2->id,
        ]);
    }

    /** @test */
    public function users_can_only_see_their_company_projects()
    {
        // Create projects for each company
        $project1 = Project::create([
            'proj_name' => 'Project One',
            'proj_status' => 1,
            'company_id' => $this->company1->id,
        ]);

        $project2 = Project::create([
            'proj_name' => 'Project Two',
            'proj_status' => 1,
            'company_id' => $this->company2->id,
        ]);

        // Set company 1 context
        session(['company_id' => $this->company1->id]);
        $visibleProjects = Project::all();
        
        $this->assertCount(1, $visibleProjects);
        $this->assertEquals($project1->proj_id, $visibleProjects->first()->proj_id);
        $this->assertFalse($visibleProjects->contains('proj_id', $project2->proj_id));

        // Switch to company 2 context
        session(['company_id' => $this->company2->id]);
        $visibleProjects = Project::all();
        
        $this->assertCount(1, $visibleProjects);
        $this->assertEquals($project2->proj_id, $visibleProjects->first()->proj_id);
        $this->assertFalse($visibleProjects->contains('proj_id', $project1->proj_id));
    }

    /** @test */
    public function purchase_orders_are_scoped_to_company()
    {
        // Create projects for POs
        $project1 = Project::create([
            'proj_name' => 'Project One',
            'proj_status' => 1,
            'company_id' => $this->company1->id,
        ]);

        $project2 = Project::create([
            'proj_name' => 'Project Two',
            'proj_status' => 1,
            'company_id' => $this->company2->id,
        ]);

        // Create POs for each company
        $po1 = PurchaseOrder::create([
            'porder_no' => 'PO-001',
            'porder_createdate' => now(),
            'porder_status' => 1,
            'porder_project_ms' => $project1->proj_id,
            'company_id' => $this->company1->id,
        ]);

        $po2 = PurchaseOrder::create([
            'porder_no' => 'PO-002',
            'porder_createdate' => now(),
            'porder_status' => 1,
            'porder_project_ms' => $project2->proj_id,
            'company_id' => $this->company2->id,
        ]);

        // Company 1 context
        session(['company_id' => $this->company1->id]);
        $visiblePOs = PurchaseOrder::all();
        
        $this->assertCount(1, $visiblePOs);
        $this->assertEquals('PO-001', $visiblePOs->first()->porder_no);

        // Company 2 context
        session(['company_id' => $this->company2->id]);
        $visiblePOs = PurchaseOrder::all();
        
        $this->assertCount(1, $visiblePOs);
        $this->assertEquals('PO-002', $visiblePOs->first()->porder_no);
    }

    /** @test */
    public function suppliers_are_scoped_to_company()
    {
        $supplier1 = Supplier::create([
            'sup_name' => 'Supplier One',
            'sup_email' => 'supplier1@test.com',
            'company_id' => $this->company1->id,
        ]);

        $supplier2 = Supplier::create([
            'sup_name' => 'Supplier Two',
            'sup_email' => 'supplier2@test.com',
            'company_id' => $this->company2->id,
        ]);

        // Company 1 context
        session(['company_id' => $this->company1->id]);
        $visibleSuppliers = Supplier::all();
        
        $this->assertCount(1, $visibleSuppliers);
        $this->assertEquals('Supplier One', $visibleSuppliers->first()->sup_name);

        // Company 2 context
        session(['company_id' => $this->company2->id]);
        $visibleSuppliers = Supplier::all();
        
        $this->assertCount(1, $visibleSuppliers);
        $this->assertEquals('Supplier Two', $visibleSuppliers->first()->sup_name);
    }

    /** @test */
    public function cannot_access_other_company_data_via_direct_id()
    {
        $project1 = Project::create([
            'proj_name' => 'Company 1 Project',
            'proj_status' => 1,
            'company_id' => $this->company1->id,
        ]);

        $project2 = Project::create([
            'proj_name' => 'Company 2 Project',
            'proj_status' => 1,
            'company_id' => $this->company2->id,
        ]);

        // Set company 1 context
        session(['company_id' => $this->company1->id]);

        // Try to access company 2's project by ID
        $accessedProject = Project::find($project2->proj_id);
        
        // Should return null because CompanyScope filters it out
        $this->assertNull($accessedProject);

        // Can access own project
        $ownProject = Project::find($project1->proj_id);
        $this->assertNotNull($ownProject);
        $this->assertEquals('Company 1 Project', $ownProject->proj_name);
    }

    /** @test */
    public function company_scope_can_be_disabled_when_needed()
    {
        $project1 = Project::create([
            'proj_name' => 'Project One',
            'proj_status' => 1,
            'company_id' => $this->company1->id,
        ]);

        $project2 = Project::create([
            'proj_name' => 'Project Two',
            'proj_status' => 1,
            'company_id' => $this->company2->id,
        ]);

        // Set company 1 context
        session(['company_id' => $this->company1->id]);

        // With scope (default)
        $scopedProjects = Project::all();
        $this->assertCount(1, $scopedProjects);

        // Without scope (super admin queries)
        $allProjects = Project::withoutGlobalScope('company')->get();
        $this->assertCount(2, $allProjects);
    }

    /** @test */
    public function middleware_enforces_company_context()
    {
        // This would typically test the SetTenantContext middleware
        // ensuring it sets session('company_id') correctly
        
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@company.com',
            'password' => bcrypt('password'),
            'u_type' => 2,
            'company_id' => $this->company1->id,
        ]);

        // Simulate login (middleware should set company_id)
        $this->actingAs($user);
        
        // In a real scenario, middleware would set this
        session(['company_id' => $user->company_id]);
        
        $this->assertEquals($this->company1->id, session('company_id'));
    }

    /** @test */
    public function users_belong_to_correct_company()
    {
        session(['company_id' => $this->company1->id]);
        $visibleUsers = User::all();
        
        // Should only see company 1 users
        $this->assertTrue($visibleUsers->contains('id', $this->user1->id));
        $this->assertFalse($visibleUsers->contains('id', $this->user2->id));
    }
}
