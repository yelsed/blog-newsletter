<?php

declare(strict_types=1);

namespace App\Data\Admin;

use App\Data\Admin\Blocks\BlockData;
use App\Data\Admin\Blocks\BlockDataFactory;
use App\Enums\EmailStatus;
use App\Models\Email;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EmailData extends Data
{
    /** @param list<BlockData> $blocks */
    public function __construct(
        public readonly string $subject,
        public readonly array $blocks,
        public readonly EmailStatus $status = EmailStatus::Draft,
        public readonly ?int $id = null,
        public readonly ?CarbonImmutable $sent_at = null,
        public readonly ?CarbonImmutable $created_at = null,
        public readonly ?CarbonImmutable $updated_at = null,
    ) {}

    public static function fromModel(Email $email): self
    {
        return new self(
            subject: $email->subject,
            blocks: array_values(array_map(
                fn (array $payload): BlockData => BlockDataFactory::fromArray($payload),
                $email->blocks,
            )),
            status: $email->status,
            id: $email->id,
            sent_at: $email->sent_at,
            created_at: $email->created_at,
            updated_at: $email->updated_at,
        );
    }
}
