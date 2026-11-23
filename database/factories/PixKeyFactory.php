<?php

namespace Database\Factories;

use App\Enums\PixKeyType;
use App\Models\Account;
use App\Models\PixKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PixKey>
 */
class PixKeyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'type' => PixKeyType::Random,
            'key' => (string) Str::uuid(),
        ];
    }
}
