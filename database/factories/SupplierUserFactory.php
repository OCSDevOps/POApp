<?php

namespace Database\Factories;

use App\Models\SupplierUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class SupplierUserFactory extends Factory
{
    protected $model = SupplierUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'),
            'status' => 1,
            'company_id' => null,
            'supplier_id' => null,
        ];
    }

    /**
     * Indicate the user is unverified.
     */
    public function unverified(): self
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
