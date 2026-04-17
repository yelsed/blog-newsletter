<?php

declare(strict_types=1);

use App\Models\Email;
use App\Models\User;
use App\Notifications\ComposedEmailNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('sends a test to the admin email by default', function (): void {
    Notification::fake();
    $email = Email::factory()->create();

    $this->actingAs($this->admin)
        ->postJson("/api/admin/emails/{$email->id}/send-test")
        ->assertAccepted();

    Notification::assertSentOnDemand(
        ComposedEmailNotification::class,
        function (ComposedEmailNotification $notification, array $channels, object $notifiable): bool {
            return property_exists($notifiable, 'routes')
                && ($notifiable->routes['mail'] ?? null) === $this->admin->email;
        },
    );
    Notification::assertCount(1);
});

it('can target a custom email address', function (): void {
    Notification::fake();
    $email = Email::factory()->create();

    $this->actingAs($this->admin)
        ->postJson("/api/admin/emails/{$email->id}/send-test", ['email' => 'qa@example.com'])
        ->assertAccepted();

    Notification::assertSentOnDemand(
        ComposedEmailNotification::class,
        function (ComposedEmailNotification $notification, array $channels, object $notifiable): bool {
            return property_exists($notifiable, 'routes')
                && ($notifiable->routes['mail'] ?? null) === 'qa@example.com';
        },
    );
});

it('rejects invalid target emails', function (): void {
    Notification::fake();
    $email = Email::factory()->create();

    $this->actingAs($this->admin)
        ->postJson("/api/admin/emails/{$email->id}/send-test", ['email' => 'not-an-email'])
        ->assertStatus(422);

    Notification::assertNothingSent();
});

it('rejects requests missing the emails.send permission', function (): void {
    Notification::fake();
    $email = Email::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson("/api/admin/emails/{$email->id}/send-test")
        ->assertForbidden();

    Notification::assertNothingSent();
});
