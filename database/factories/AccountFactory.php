<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'agency' => '0001',
            'number' => fake()->unique()->numerify('######').'-'.fake()->randomDigit(),
            'status' => AccountStatus::Active,
        ];
    }

    public function blocked(): static
    {
        return $this->state(['status' => AccountStatus::Blocked]);
    }
}
