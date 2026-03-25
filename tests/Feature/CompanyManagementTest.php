<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;

class CompanyManagementTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $superAdmin;
    protected $regularUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test company
        $this->company = Company::create([
            'name' => 'Test Company',
            'subdomain' => 'test',
            'status' => 1,
        ]);

        // Create super admin user
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'u_type' => 1, // Super admin
            'company_id' => $this->company->id,
        ]);

        // Create regular user
        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'u_type' => 2, // Regular user
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function super_admin_can_view_companies_index()
    {
        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->get(route('admin.companies.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.index');
        $response->assertSee('Manage Companies');
    }

    /** @test */
    public function regular_user_cannot_access_companies_index()
    {
        $response = $this->actingAs($this->regularUser)
            ->withSession(['u_type' => 2, 'company_id' => $this->company->id])
            ->get(route('admin.companies.index'));

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function super_admin_can_create_company()
    {
        $newCompanyData = [
            'name' => 'New Company',
            'subdomain' => 'newco',
            'status' => 1,
        ];

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->post(route('admin.companies.store'), $newCompanyData);

        $response->assertRedirect(route('admin.companies.index'));
        $this->assertDatabaseHas('companies', [
            'name' => 'New Company',
            'subdomain' => 'newco',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_company()
    {
        $newCompanyData = [
            'name' => 'Unauthorized Company',
            'subdomain' => 'unauthorized',
            'status' => 1,
        ];

        $response = $this->actingAs($this->regularUser)
            ->withSession(['u_type' => 2, 'company_id' => $this->company->id])
            ->post(route('admin.companies.store'), $newCompanyData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('companies', [
            'name' => 'Unauthorized Company',
        ]);
    }

    /** @test */
    public function super_admin_can_update_company()
    {
        $updateData = [
            'name' => 'Updated Company Name',
            'subdomain' => $this->company->subdomain,
            'status' => 0, // Inactive
        ];

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->put(route('admin.companies.update', $this->company), $updateData);

        $response->assertRedirect(route('admin.companies.index'));
        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => 'Updated Company Name',
            'status' => 0,
        ]);
    }

    /** @test */
    public function super_admin_can_switch_company_context()
    {
        $secondCompany = Company::create([
            'name' => 'Second Company',
            'subdomain' => 'second',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->post(route('admin.companies.switch', $secondCompany));

        $response->assertRedirect();
        $response->assertSessionHas('company_id', $secondCompany->id);
    }

    /** @test */
    public function regular_user_cannot_switch_company_context()
    {
        $secondCompany = Company::create([
            'name' => 'Second Company',
            'subdomain' => 'second',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->regularUser)
            ->withSession(['u_type' => 2, 'company_id' => $this->company->id])
            ->post(route('admin.companies.switch', $secondCompany));

        $response->assertStatus(403);
        $response->assertSessionHas('company_id', $this->company->id); // Unchanged
    }

    /** @test */
    public function cannot_delete_company_with_users()
    {
        // Company already has users (created in setUp)
        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->delete(route('admin.companies.destroy', $this->company));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
        ]);
    }

    /** @test */
    public function can_delete_empty_company()
    {
        $emptyCompany = Company::create([
            'name' => 'Empty Company',
            'subdomain' => 'empty',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->delete(route('admin.companies.destroy', $emptyCompany));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('companies', [
            'id' => $emptyCompany->id,
        ]);
    }

    /** @test */
    public function company_name_is_required()
    {
        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->post(route('admin.companies.store'), [
                'name' => '',
                'subdomain' => 'test',
                'status' => 1,
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function subdomain_auto_generates_if_empty()
    {
        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->post(route('admin.companies.store'), [
                'name' => 'Auto Subdomain Company',
                'subdomain' => '',
                'status' => 1,
            ]);

        $response->assertRedirect();
        
        $company = Company::where('name', 'Auto Subdomain Company')->first();
        $this->assertNotNull($company);
        $this->assertNotEmpty($company->subdomain);
    }

    /** @test */
    public function subdomain_must_be_unique()
    {
        $response = $this->actingAs($this->superAdmin)
            ->withSession(['u_type' => 1, 'company_id' => $this->company->id])
            ->post(route('admin.companies.store'), [
                'name' => 'Duplicate Subdomain',
                'subdomain' => $this->company->subdomain, // Use existing subdomain
                'status' => 1,
            ]);

        $response->assertSessionHasErrors('subdomain');
    }
}
