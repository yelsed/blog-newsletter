<?php

declare(strict_types=1);

use App\Models\Email;
use App\Models\User;

it('rejects unauthenticated requests to the admin emails resource', function (): void {
    $this->getJson('/api/admin/emails')->assertUnauthorized();
    $this->postJson('/api/admin/emails', [])->assertUnauthorized();
});

it('rejects authenticated users without the admin role', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/admin/emails')
        ->assertForbidden();

    $this->actingAs($user)
        ->postJson('/api/admin/emails', [
            'subject' => 'Hello',
            'blocks' => [['type' => 'text', 'body' => 'hi']],
        ])
        ->assertForbidden();
});

it('lets an admin list emails', function (): void {
    $admin = adminUser();
    Email::factory()->count(3)->create();

    $this->actingAs($admin)
        ->getJson('/api/admin/emails')
        ->assertOk();
});
