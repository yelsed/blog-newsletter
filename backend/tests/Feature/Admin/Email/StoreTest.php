<?php

declare(strict_types=1);

use App\Enums\EmailStatus;
use App\Models\Email;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('stores a draft email with all supported block types', function (): void {
    $payload = [
        'subject' => 'Hello world',
        'blocks' => [
            ['type' => 'text', 'body' => 'First paragraph', 'align' => 'left'],
            ['type' => 'link', 'label' => 'Docs', 'href' => 'https://example.com'],
            ['type' => 'list', 'items' => ['one', 'two'], 'ordered' => true],
            ['type' => 'image', 'url' => 'https://example.com/i.png', 'alt' => 'pic'],
            ['type' => 'gif', 'url' => 'https://example.com/a.gif', 'alt' => 'anim'],
            ['type' => 'button', 'label' => 'Go', 'href' => 'https://example.com'],
        ],
    ];

    $response = $this->actingAs($this->admin)
        ->postJson('/api/admin/emails', $payload)
        ->assertCreated()
        ->assertJsonPath('subject', 'Hello world')
        ->assertJsonPath('status', 'draft')
        ->assertJsonCount(6, 'blocks');

    expect(Email::count())->toBe(1);

    $email = Email::first();
    expect($email->status)->toBe(EmailStatus::Draft)
        ->and($email->blocks)->toHaveCount(6)
        ->and($email->user_id)->toBe($this->admin->id);
});

it('rejects missing subject', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/emails', ['blocks' => [['type' => 'text', 'body' => 'x']]])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['subject']);
});

it('rejects empty blocks array', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/emails', ['subject' => 'Hi', 'blocks' => []])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['blocks']);
});

it('rejects an unknown block type', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/emails', [
            'subject' => 'Hi',
            'blocks' => [['type' => 'quiz']],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['blocks.0.type']);
});

it('rejects a text block missing a body', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/emails', [
            'subject' => 'Hi',
            'blocks' => [['type' => 'text']],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['blocks.0.body']);
});

it('rejects a button block with an invalid href', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/emails', [
            'subject' => 'Hi',
            'blocks' => [['type' => 'button', 'label' => 'Go', 'href' => 'not-a-url']],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['blocks.0.href']);
});

it('rejects a list block with no items', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/emails', [
            'subject' => 'Hi',
            'blocks' => [['type' => 'list', 'items' => []]],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['blocks.0.items']);
});
