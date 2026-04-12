<?php

declare(strict_types=1);

namespace App\Data\Newsletter;

use App\Models\Subscriber;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SubscriberData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly ?string $name,
        public readonly ?CarbonImmutable $email_verified_at,
        public readonly ?CarbonImmutable $subscribed_at,
    ) {}

    public static function fromModel(Subscriber $subscriber): self
    {
        return new self(
            id: $subscriber->id,
            email: $subscriber->email,
            name: $subscriber->name,
            email_verified_at: $subscriber->email_verified_at,
            subscribed_at: $subscriber->subscribed_at,
        );
    }
}
