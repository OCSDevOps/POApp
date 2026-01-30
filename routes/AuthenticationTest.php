<?php

namespace Tests\Feature\Supplier;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $supplier;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company and a supplier to associate the user with
        $this->company = Company::factory()->create();
        $this->supplier = Supplier::factory()->create(['company_id' => $this->company->id]);

        $this->user = SupplierUser::factory()->create([
            'company_id' => $this->company->id,
            'supplier_id' => $this->supplier->sup_id,
            'password' => bcrypt('password'),
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
    public function supplier_cannot_view_login_page_when_authenticated()
    {
        $response = $this->actingAs($this->user, 'supplier')->get(route('supplier.login'));
        $response->assertRedirect(route('supplier.dashboard'));
    }

    /** @test */
    public function supplier_can_login_with_correct_credentials()
    {
        $response = $this->post(route('supplier.login.submit'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('supplier.dashboard'));
        $this->assertAuthenticatedAs($this->user, 'supplier');
    }

    /** @test */
    public function supplier_cannot_login_with_incorrect_credentials()
    {
        $response = $this->post(route('supplier.login.submit'), [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('supplier');
    }

    /** @test */
    public function supplier_can_logout()
    {
        $this->actingAs($this->user, 'supplier');
        $this->assertAuthenticated('supplier');

        $response = $this->post(route('supplier.logout'));

        $response->assertRedirect(route('supplier.login'));
        $this->assertGuest('supplier');
    }

    /** @test */
    public function disabled_supplier_user_cannot_login()
    {
        $this->user->update(['status' => 0]); // Assuming status 0 is inactive

        $response = $this->post(route('supplier.login.submit'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        // The 'credentials' method in AuthController now checks for 'status' => 1
        $response->assertSessionHasErrors('email');
        $this->assertGuest('supplier');
    }

    /** @test */
    public function staff_user_cannot_login_to_supplier_portal()
    {
        $staffUser = \App\Models\User::factory()->create([
            'company_id' => $this->user->company_id,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('supplier.login.submit'), [
            'email' => $staffUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('supplier');
    }
}