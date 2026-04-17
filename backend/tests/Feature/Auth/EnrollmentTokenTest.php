<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;

it('logs the admin in when the enrollment token is valid', function (): void {
    $admin = adminUser();
    Cache::put('passkey:enroll:abc123', $admin->id, now()->addMinutes(10));

    $this->postJson('/api/auth/enroll/abc123')
        ->assertOk()
        ->assertJsonPath('email', $admin->email);

    $this->getJson('/api/user')->assertOk()->assertJsonPath('id', $admin->id);

    expect(Cache::has('passkey:enroll:abc123'))->toBeFalse();
});

it('rejects an invalid or expired enrollment token', function (): void {
    $this->postJson('/api/auth/enroll/does-not-exist')
        ->assertNotFound();
});

it('consumes the token so it can only be used once', function (): void {
    $admin = adminUser();
    Cache::put('passkey:enroll:single-use', $admin->id, now()->addMinutes(10));

    $this->postJson('/api/auth/enroll/single-use')->assertOk();

    $this->postJson('/api/auth/enroll/single-use')->assertNotFound();
});
