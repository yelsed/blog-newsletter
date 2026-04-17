<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EmailStatus;
use App\Models\Email;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Email>
 */
class EmailFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject' => $this->faker->sentence(),
            'blocks' => [
                ['type' => 'text', 'body' => $this->faker->paragraph(), 'align' => 'left'],
            ],
            'status' => EmailStatus::Draft,
            'sent_at' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => EmailStatus::Sent,
            'sent_at' => now(),
        ]);
    }
}
