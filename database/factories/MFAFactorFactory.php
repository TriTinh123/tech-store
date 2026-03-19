<?php

namespace Database\Factories;

use App\Models\MFAFactor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MFAFactorFactory extends Factory
{
    protected $model = MFAFactor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['totp', 'sms', 'email', 'webauthn']),
            'secret' => $this->faker->regexify('[A-Z2-7]{32}'),
            'verified_value' => null,
            'name' => $this->faker->words(2, true),
            'is_verified' => false,
            'is_primary' => false,
            'status' => 'active',
            'counter' => 0,
            'metadata' => null,
            'verified_at' => null,
            'last_used_at' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function totp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'totp',
        ]);
    }

    public function backupCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'backup_code',
        ]);
    }
}
