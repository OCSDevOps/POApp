<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
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
        $this->user1 = User::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'User One',
            'email' => 'user1@company1.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company1->id,
        ]);

        $this->user2 = User::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'User Two',
            'email' => 'user2@company2.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company2->id,
        ]);
    }

    /** @test */
    public function users_can_only_see_their_company_projects()
    {
        // Create projects for each company
        $project1 = Project::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'proj_name' => 'Project Company 1',
            'proj_status' => 1,
            'company_id' => $this->company1->id,
        ]);

        $project2 = Project::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'proj_name' => 'Project Company 2',
            'proj_status' => 1,
            'company_id' => $this->company2->id,
        ]);

        // Login as user1 and check they only see company1 projects
        session(['company_id' => $this->company1->id]);
        $projects = Project::all();
        $this->assertCount(1, $projects);
        $this->assertEquals('Project Company 1', $projects->first()->proj_name);

        // Switch to user2 and check they only see company2 projects
        session(['company_id' => $this->company2->id]);
        $projects = Project::all();
        $this->assertCount(1, $projects);
        $this->assertEquals('Project Company 2', $projects->first()->proj_name);
    }

    /** @test */
    public function users_can_only_see_their_company_suppliers()
    {
        // Create suppliers for each company
        $supplier1 = Supplier::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'sup_name' => 'Supplier Company 1',
            'sup_status' => 1,
            'company_id' => $this->company1->id,
        ]);

        $supplier2 = Supplier::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'sup_name' => 'Supplier Company 2',
            'sup_status' => 1,
            'company_id' => $this->company2->id,
        ]);

        session(['company_id' => $this->company1->id]);
        $this->assertCount(1, Supplier::all());

        session(['company_id' => $this->company2->id]);
        $this->assertCount(1, Supplier::all());
    }

    /** @test */
    public function users_can_only_see_their_company_purchase_orders()
    {
        // Create POs for each company
        $po1 = PurchaseOrder::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'porder_no' => 'PO-001',
            'porder_status' => 1,
            'company_id' => $this->company1->id,
        ]);

        $po2 = PurchaseOrder::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'porder_no' => 'PO-002',
            'porder_status' => 1,
            'company_id' => $this->company2->id,
        ]);

        session(['company_id' => $this->company1->id]);
        $this->assertCount(1, PurchaseOrder::all());
        $this->assertEquals('PO-001', PurchaseOrder::first()->porder_no);

        session(['company_id' => $this->company2->id]);
        $this->assertCount(1, PurchaseOrder::all());
        $this->assertEquals('PO-002', PurchaseOrder::first()->porder_no);
    }

    /** @test */
    public function tenant_context_is_set_from_authenticated_user()
    {
        $this->actingAs($this->user1);

        $response = $this->get('/admin/dashboard');
        
        $this->assertEquals($this->company1->id, session('company_id'));
    }

    /** @test */
    public function without_global_scope_shows_all_data()
    {
        Project::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'proj_name' => 'Project 1',
            'proj_status' => 1,
            'company_id' => $this->company1->id,
        ]);

        Project::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'proj_name' => 'Project 2',
            'proj_status' => 1,
            'company_id' => $this->company2->id,
        ]);

        // Without scope, see all projects
        $allProjects = Project::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->get();
        $this->assertCount(2, $allProjects);

        // With scope, see only company's projects
        session(['company_id' => $this->company1->id]);
        $companyProjects = Project::all();
        $this->assertCount(1, $companyProjects);
    }
}
