<?php

declare(strict_types=1);

use App\Models\Email;

beforeEach(function (): void {
    $this->admin = adminUser();
});

it('deletes an email', function (): void {
    $email = Email::factory()->create();

    $this->actingAs($this->admin)
        ->deleteJson("/api/admin/emails/{$email->id}")
        ->assertNoContent();

    expect(Email::find($email->id))->toBeNull();
});
