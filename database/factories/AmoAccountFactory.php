<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AmoAccount;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AmoAccount>
 */
class AmoAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'account_id' => random_int(1, 999999),
            'base_domain' => fake()->domainName(),
            'access_token' => Str::random(64),
            'refresh_token' => Str::random(128),
            'expires' => random_int(1694000000, 1794000000),
        ];
    }
}
