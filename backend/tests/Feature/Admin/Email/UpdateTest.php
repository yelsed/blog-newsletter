<?php

declare(strict_types=1);

use App\Enums\EmailStatus;
use App\Models\Email;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('updates a draft email', function (): void {
    $email = Email::factory()->create([
        'subject' => 'Old',
        'blocks' => [['type' => 'text', 'body' => 'old', 'align' => 'left']],
    ]);

    $this->actingAs($this->admin)
        ->putJson("/api/admin/emails/{$email->id}", [
            'subject' => 'New',
            'blocks' => [['type' => 'text', 'body' => 'new', 'align' => 'center']],
        ])
        ->assertOk()
        ->assertJsonPath('subject', 'New');

    expect($email->fresh()->subject)->toBe('New')
        ->and($email->fresh()->blocks[0]['body'])->toBe('new')
        ->and($email->fresh()->blocks[0]['align'])->toBe('center');
});

it('refuses to update an already-sent email', function (): void {
    $email = Email::factory()->sent()->create();

    $this->actingAs($this->admin)
        ->putJson("/api/admin/emails/{$email->id}", [
            'subject' => 'New',
            'blocks' => [['type' => 'text', 'body' => 'x', 'align' => 'left']],
        ])
        ->assertForbidden();

    expect($email->fresh()->status)->toBe(EmailStatus::Sent);
});
