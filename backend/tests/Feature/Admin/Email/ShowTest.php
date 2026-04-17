<?php

declare(strict_types=1);

use App\Models\Email;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('returns a single email with its blocks', function (): void {
    $email = Email::factory()->create([
        'subject' => 'Newsletter #1',
        'blocks' => [
            ['type' => 'text', 'body' => 'Welcome', 'align' => 'left'],
        ],
    ]);

    $this->actingAs($this->admin)
        ->getJson("/api/admin/emails/{$email->id}")
        ->assertOk()
        ->assertJsonPath('subject', 'Newsletter #1')
        ->assertJsonPath('blocks.0.type', 'text')
        ->assertJsonPath('blocks.0.body', 'Welcome');
});

it('returns 404 for an unknown email', function (): void {
    $this->actingAs($this->admin)
        ->getJson('/api/admin/emails/999999')
        ->assertNotFound();
});
