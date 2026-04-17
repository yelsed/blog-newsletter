<?php

declare(strict_types=1);

use App\Models\Email;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('lists emails paginated', function (): void {
    Email::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->getJson('/api/admin/emails')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('filters by status', function (): void {
    Email::factory()->count(2)->create();
    Email::factory()->sent()->count(3)->create();

    $this->actingAs($this->admin)
        ->getJson('/api/admin/emails?status=sent')
        ->assertOk()
        ->assertJsonCount(3, 'data');

    $this->actingAs($this->admin)
        ->getJson('/api/admin/emails?status=draft')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});
