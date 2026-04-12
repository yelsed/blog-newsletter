<?php

declare(strict_types=1);

namespace App\Data\Newsletter;

use Spatie\LaravelData\Data;

class SubscribeData extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $name = null,
    ) {}
}
