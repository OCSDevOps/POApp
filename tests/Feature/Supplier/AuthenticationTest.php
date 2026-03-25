<?php

namespace Tests\Feature\Supplier;

use App\Models\SupplierUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function supplier_can_register_and_is_redirected_to_verification_notice()
    {
        $response = $this->post('/supplier/register', [
            'name' => 'Supplier Tester',
            'email' => 'supplier@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/supplier/email/verify');
        $this->assertDatabaseHas('supplier_users', ['email' => 'supplier@example.com']);
    }

    /** @test */
    public function supplier_can_login_with_correct_credentials()
    {
        $user = SupplierUser::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('secret123'),
            'status' => 1,
        ]);

        $response = $this->post('/supplier/login', [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('supplier.dashboard'));
        $this->assertAuthenticatedAs($user, 'supplier');
    }

    /** @test */
    public function inactive_supplier_cannot_login()
    {
        SupplierUser::factory()->create([
            'email' => 'disabled@example.com',
            'password' => Hash::make('secret123'),
            'status' => 0,
        ]);

        $response = $this->post('/supplier/login', [
            'email' => 'disabled@example.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('supplier');
    }
}
