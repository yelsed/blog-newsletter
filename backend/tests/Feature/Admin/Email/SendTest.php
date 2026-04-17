<?php

declare(strict_types=1);

use App\Actions\Admin\SendEmailAction;
use App\Enums\EmailStatus;
use App\Models\Email;
use App\Models\Subscriber;
use App\Notifications\ComposedEmailNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('queues a notification to every active subscriber and marks the email sent', function (): void {
    Notification::fake();

    $verified = Subscriber::factory()->count(3)->create([
        'email_verified_at' => now(),
    ]);
    Subscriber::factory()->count(2)->create(['email_verified_at' => null]);

    $email = Email::factory()->create();

    $this->actingAs($this->admin)
        ->postJson("/api/admin/emails/{$email->id}/send")
        ->assertAccepted();

    foreach ($verified as $subscriber) {
        Notification::assertSentOnDemand(
            ComposedEmailNotification::class,
            function (ComposedEmailNotification $notification, array $channels, object $notifiable) use ($subscriber): bool {
                return property_exists($notifiable, 'routes')
                    && ($notifiable->routes['mail'] ?? null) === $subscriber->email;
            },
        );
    }

    Notification::assertCount(3);

    expect($email->fresh()->status)->toBe(EmailStatus::Sent)
        ->and($email->fresh()->sent_at)->not->toBeNull();
});

it('refuses to send an already-sent email', function (): void {
    Notification::fake();
    $email = Email::factory()->sent()->create();

    $this->actingAs($this->admin)
        ->postJson("/api/admin/emails/{$email->id}/send")
        ->assertStatus(409);

    Notification::assertNothingSent();
});

it('fans out only once when the action is executed twice (retry safety)', function (): void {
    Notification::fake();
    Subscriber::factory()->count(3)->create(['email_verified_at' => now()]);
    $email = Email::factory()->create();

    app(SendEmailAction::class)->execute($email);
    app(SendEmailAction::class)->execute($email);

    Notification::assertCount(3);
    expect($email->fresh()->status)->toBe(EmailStatus::Sent);
});
