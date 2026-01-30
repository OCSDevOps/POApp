<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupplierUserFactory extends Factory
{
    protected $model = SupplierUser::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'company_id' => Company::factory(),
            'supplier_id' => Supplier::factory(),
            'status' => 1,
        ];
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return ['email_verified_at' => null];
        });
    }
}