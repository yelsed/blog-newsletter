<?php

declare(strict_types=1);

namespace App\Data\Dev;

use Spatie\LaravelData\Data;

class EmailPreviewData extends Data
{
    /** @param array<string, mixed> $variables */
    public function __construct(
        public string $template,
        public string $title,
        public string $url,
        public array $variables,
    ) {}
}
