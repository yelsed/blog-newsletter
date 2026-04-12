<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Subscriber> */
class SubscriberFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => [
            'email_verified_at' => now(),
        ]);
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (): array => [
            'email_verified_at' => now(),
            'unsubscribed_at' => now(),
        ]);
    }
}
