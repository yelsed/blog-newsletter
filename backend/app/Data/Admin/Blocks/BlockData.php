<?php

declare(strict_types=1);

namespace App\Data\Admin\Blocks;

use App\Enums\BlockType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
abstract class BlockData extends Data
{
    public function __construct(
        public readonly BlockType $type,
    ) {}
}
