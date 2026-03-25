<?php

namespace Tests\Feature\Supplier;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SupplierAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    protected $company;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test company
        $this->company = Company::create([
            'name' => 'Test Construction Co',
            'subdomain' => 'test',
            'status' => 1,
        ]);

        // Create test supplier
        $this->supplier = Supplier::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'sup_name' => 'Test Supplier Inc',
            'sup_email' => 'supplier@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function supplier_can_view_login_page()
    {
        $response = $this->get(route('supplier.login'));
        $response->assertStatus(200);
        $response->assertViewIs('supplier.auth.login');
    }

    /** @test */
    public function supplier_can_register()
    {
        $response = $this->post(route('supplier.register.submit'), [
            'name' => 'John Supplier',
            'email' => 'john@supplier.com',
            'phone' => '555-0100',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'supplier_id' => $this->supplier->sup_id,
        ]);

        $response->assertRedirect(route('supplier.verification.notice'));
        $this->assertDatabaseHas('supplier_users', [
            'email' => 'john@supplier.com',
            'supplier_id' => $this->supplier->sup_id,
        ]);
    }

    /** @test */
    public function supplier_can_login_with_valid_credentials()
    {
        $supplierUser = SupplierUser::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'Test Supplier User',
            'email' => 'test@supplier.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('supplier.login.submit'), [
            'email' => 'test@supplier.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('supplier.dashboard'));
        $this->assertAuthenticatedAs($supplierUser, 'supplier');
        $this->assertEquals($this->company->id, session('company_id'));
    }

    /** @test */
    public function supplier_cannot_login_with_invalid_credentials()
    {
        $supplierUser = SupplierUser::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'Test Supplier User',
            'email' => 'test@supplier.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
        ]);

        $response = $this->post(route('supplier.login.submit'), [
            'email' => 'test@supplier.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('supplier');
    }

    /** @test */
    public function supplier_cannot_login_if_inactive()
    {
        $supplierUser = SupplierUser::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'Inactive Supplier',
            'email' => 'inactive@supplier.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 0, // Inactive
        ]);

        $response = $this->post(route('supplier.login.submit'), [
            'email' => 'inactive@supplier.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('supplier');
    }

    /** @test */
    public function supplier_can_access_dashboard_when_authenticated()
    {
        $supplierUser = SupplierUser::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'Test Supplier User',
            'email' => 'test@supplier.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($supplierUser, 'supplier')
            ->get(route('supplier.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('supplier.dashboard');
    }

    /** @test */
    public function supplier_cannot_access_dashboard_when_not_authenticated()
    {
        $response = $this->get(route('supplier.dashboard'));
        $response->assertRedirect(route('supplier.login'));
    }

    /** @test */
    public function supplier_can_logout()
    {
        $supplierUser = SupplierUser::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'Test Supplier User',
            'email' => 'test@supplier.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($supplierUser, 'supplier');
        
        $response = $this->post(route('supplier.logout'));
        
        $response->assertRedirect(route('supplier.login'));
        $this->assertGuest('supplier');
    }

    /** @test */
    public function supplier_tenant_context_is_set_on_login()
    {
        $supplierUser = SupplierUser::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'Test Supplier User',
            'email' => 'test@supplier.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $this->post(route('supplier.login.submit'), [
            'email' => 'test@supplier.com',
            'password' => 'password',
        ]);

        $this->assertEquals($this->company->id, session('company_id'));
    }

    /** @test */
    public function supplier_can_update_profile()
    {
        $supplierUser = SupplierUser::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->create([
            'name' => 'Old Name',
            'email' => 'test@supplier.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($supplierUser, 'supplier')
            ->post(route('supplier.profile.update'), [
                'name' => 'New Name',
                'phone' => '555-9999',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('supplier_users', [
            'id' => $supplierUser->id,
            'name' => 'New Name',
            'phone' => '555-9999',
        ]);
    }
}
