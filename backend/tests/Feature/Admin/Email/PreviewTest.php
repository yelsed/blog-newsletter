<?php

declare(strict_types=1);

use App\Contracts\MediaStorage;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('renders HTML containing fragments for each block type', function (): void {
    $this->mock(MediaStorage::class, function ($mock): void {
        $mock->shouldReceive('url')
            ->andReturnUsing(fn (string $path): string => 'https://cdn.example.test/'.$path);
    });

    $response = $this->actingAs($this->admin)
        ->postJson('/api/admin/emails/preview', [
            'subject' => 'Sample',
            'blocks' => [
                ['type' => 'text', 'body' => 'Hello from the text block', 'align' => 'center'],
                ['type' => 'link', 'label' => 'DocsLink', 'href' => 'https://example.com/docs'],
                ['type' => 'list', 'items' => ['apples', 'pears'], 'ordered' => false],
                ['type' => 'image', 'path' => 'emails/1/cat.png', 'alt' => 'A cat'],
                ['type' => 'gif', 'path' => 'emails/1/wave.gif', 'alt' => 'Wave'],
                ['type' => 'button', 'label' => 'OpenIt', 'href' => 'https://example.com/open'],
            ],
        ])
        ->assertOk();

    $html = $response->json('html');

    expect($html)->toContain('Hello from the text block')
        ->toContain('DocsLink')
        ->toContain('https://example.com/docs')
        ->toContain('<ul')
        ->toContain('apples')
        ->toContain('pears')
        ->toContain('https://cdn.example.test/emails/1/cat.png')
        ->toContain('alt="A cat"')
        ->toContain('https://cdn.example.test/emails/1/wave.gif')
        ->toContain('OpenIt')
        ->toContain('https://example.com/open');
});

it('renders an ordered list when ordered is true', function (): void {
    $response = $this->actingAs($this->admin)
        ->postJson('/api/admin/emails/preview', [
            'subject' => 'Ordered',
            'blocks' => [
                ['type' => 'list', 'items' => ['first', 'second'], 'ordered' => true],
            ],
        ])
        ->assertOk();

    expect($response->json('html'))->toContain('<ol');
});

it('rejects preview requests without auth', function (): void {
    $this->postJson('/api/admin/emails/preview', [
        'subject' => 'x',
        'blocks' => [['type' => 'text', 'body' => 'x']],
    ])->assertUnauthorized();
});
