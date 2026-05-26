<?php

declare(strict_types=1);

use App\Contracts\MediaStorage;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\UploadedFile;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('rejects unauthenticated uploads', function (): void {
    $this->postJson('/api/admin/media', [
        'file' => UploadedFile::fake()->create('x.png', 10, 'image/png'),
    ])->assertUnauthorized();
});

it('rejects authenticated non-admin uploads', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/admin/media', [
            'file' => UploadedFile::fake()->create('x.png', 10, 'image/png'),
        ])
        ->assertForbidden();
});

it('uploads a file and returns a path scoped to the email id', function (): void {
    $email = Email::factory()->create();
    $stored = null;

    $this->mock(MediaStorage::class, function ($mock) use (&$stored, $email) {
        $mock->shouldReceive('put')
            ->once()
            ->andReturnUsing(function (UploadedFile $file, string $directory) use (&$stored, $email) {
                expect($directory)->toBe('emails/'.$email->id);
                $stored = $directory.'/'.$file->getClientOriginalName();

                return $stored;
            });
        $mock->shouldReceive('url')
            ->once()
            ->andReturn('https://example.test/abc.png');
    });

    $this->actingAs($this->admin)
        ->postJson('/api/admin/media', [
            'file' => UploadedFile::fake()->create('abc.png', 10, 'image/png'),
            'emailId' => $email->id,
        ])
        ->assertCreated()
        ->assertJsonPath('path', $stored)
        ->assertJsonPath('url', 'https://example.test/abc.png');
});

it('falls back to a uploads/{ulid} directory when no emailId is provided', function (): void {
    $this->mock(MediaStorage::class, function ($mock) {
        $mock->shouldReceive('put')
            ->once()
            ->andReturnUsing(function (UploadedFile $_file, string $directory) {
                expect($directory)->toStartWith('uploads/');

                return $directory.'/generated.png';
            });
        $mock->shouldReceive('url')->once()->andReturn('https://example.test/generated.png');
    });

    $this->actingAs($this->admin)
        ->postJson('/api/admin/media', [
            'file' => UploadedFile::fake()->create('a.png', 10, 'image/png'),
        ])
        ->assertCreated();
});

it('rejects disallowed mime types', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/media', [
            'file' => UploadedFile::fake()->create('evil.svg', 10, 'image/svg+xml'),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['file']);
});

it('rejects files over the size cap', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/media', [
            'file' => UploadedFile::fake()->create('big.png', 6 * 1024, 'image/png'),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['file']);
});

it('rejects missing file', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/admin/media', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['file']);
});
