<?php

declare(strict_types=1);

use App\Models\Subscriber;
use App\Notifications\VerifySubscriptionNotification;
use Illuminate\Support\Facades\Notification;

it('subscribes with a valid email', function (): void {
    Notification::fake();

    $response = $this->postJson('/api/newsletter/subscribe', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'You have been subscribed. Please check your email to verify your subscription.');

    $this->assertDatabaseHas('subscribers', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    Notification::assertSentOnDemand(VerifySubscriptionNotification::class);
});

it('lowercases and trims the email before saving', function (): void {
    Notification::fake();

    $this->postJson('/api/newsletter/subscribe', [
        'email' => '  Test@Example.COM  ',
    ])->assertCreated();

    $this->assertDatabaseHas('subscribers', [
        'email' => 'test@example.com',
    ]);
});

it('rejects duplicate emails', function (): void {
    Subscriber::factory()->create(['email' => 'taken@example.com']);

    $response = $this->postJson('/api/newsletter/subscribe', [
        'email' => 'taken@example.com',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('rejects invalid email format', function (): void {
    $response = $this->postJson('/api/newsletter/subscribe', [
        'email' => 'not-an-email',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('rejects missing email', function (): void {
    $response = $this->postJson('/api/newsletter/subscribe', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('generates a verification token on creation', function (): void {
    Notification::fake();

    $this->postJson('/api/newsletter/subscribe', [
        'email' => 'token@example.com',
    ])->assertCreated();

    $subscriber = Subscriber::where('email', 'token@example.com')->first();

    expect($subscriber->verification_token)->toBeString()->toHaveLength(64);
    expect($subscriber->subscribed_at)->not->toBeNull();
});
