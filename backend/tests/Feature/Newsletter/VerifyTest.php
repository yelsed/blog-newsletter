<?php

declare(strict_types=1);

use App\Models\Subscriber;

it('verifies a subscriber with a valid token', function (): void {
    $subscriber = Subscriber::factory()->create();

    expect($subscriber->email_verified_at)->toBeNull();

    $response = $this->getJson("/api/newsletter/verify/{$subscriber->verification_token}");

    $response->assertOk()
        ->assertJsonPath('message', 'Your email has been verified. Welcome!');

    expect($subscriber->fresh()->email_verified_at)->not->toBeNull();
});

it('returns 404 for an invalid token', function (): void {
    $response = $this->getJson('/api/newsletter/verify/invalid-token');

    $response->assertNotFound();
});

it('returns 404 for an already verified subscriber', function (): void {
    $subscriber = Subscriber::factory()->verified()->create();

    $response = $this->getJson("/api/newsletter/verify/{$subscriber->verification_token}");

    $response->assertNotFound();
});
