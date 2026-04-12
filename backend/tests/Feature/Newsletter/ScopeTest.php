<?php

declare(strict_types=1);

use App\Models\Subscriber;

it('filters verified subscribers', function (): void {
    Subscriber::factory()->create();
    Subscriber::factory()->verified()->create();
    Subscriber::factory()->verified()->create();

    expect(Subscriber::verified()->count())->toBe(2);
});

it('filters active subscribers (verified and not unsubscribed)', function (): void {
    Subscriber::factory()->create();
    Subscriber::factory()->verified()->create();
    Subscriber::factory()->unsubscribed()->create();

    expect(Subscriber::active()->count())->toBe(1);
});
