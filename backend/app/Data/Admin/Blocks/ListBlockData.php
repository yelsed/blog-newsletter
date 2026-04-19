<?php

declare(strict_types=1);

namespace App\Data\Admin\Blocks;

use App\Enums\BlockType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ListBlockData extends BlockData
{
    /** @param list<string> $items */
    public function __construct(
        public readonly array $items,
        public readonly bool $ordered = false,
    ) {
        parent::__construct(BlockType::ListItems);
    }

    /** @return array<string, array<int, string>> */
    public static function validationRules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*' => ['required', 'string', 'max:500'],
            'ordered' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function previewRules(): array
    {
        return [
            'items' => ['sometimes', 'array', 'max:50'],
            'items.*' => ['sometimes', 'string', 'max:500'],
            'ordered' => ['sometimes', 'boolean'],
        ];
    }
}
