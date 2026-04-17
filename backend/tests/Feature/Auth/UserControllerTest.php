<?php

declare(strict_types=1);

use App\Models\User;

it('returns 401 when unauthenticated', function (): void {
    $this->getJson('/api/user')->assertUnauthorized();
});

it('returns the authenticated user with roles', function (): void {
    $admin = adminUser();

    $this->actingAs($admin)
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('email', $admin->email)
        ->assertJsonPath('roles', ['admin']);
});

it('returns a user with no roles as empty array', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('email', $user->email)
        ->assertJsonPath('roles', []);
});
