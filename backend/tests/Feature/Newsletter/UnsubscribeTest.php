<?php

declare(strict_types=1);

use App\Models\Subscriber;

it('unsubscribes a verified subscriber', function (): void {
    $subscriber = Subscriber::factory()->verified()->create();

    $response = $this->getJson("/api/newsletter/unsubscribe/{$subscriber->verification_token}");

    $response->assertOk()
        ->assertJsonPath('message', 'You have been unsubscribed. We are sorry to see you go.');

    expect($subscriber->fresh()->unsubscribed_at)->not->toBeNull();
});

it('returns 404 for an unverified subscriber', function (): void {
    $subscriber = Subscriber::factory()->create();

    $response = $this->getJson("/api/newsletter/unsubscribe/{$subscriber->verification_token}");

    $response->assertNotFound();
});

it('returns 404 for an already unsubscribed subscriber', function (): void {
    $subscriber = Subscriber::factory()->unsubscribed()->create();

    $response = $this->getJson("/api/newsletter/unsubscribe/{$subscriber->verification_token}");

    $response->assertNotFound();
});

it('returns 404 for an invalid token', function (): void {
    $response = $this->getJson('/api/newsletter/unsubscribe/invalid-token');

    $response->assertNotFound();
});
